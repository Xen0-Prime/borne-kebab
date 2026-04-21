<?php

namespace App\Controller;

use App\Entity\LignePanierMenu;
use App\Entity\LignePanierMenuDetail;
use App\Entity\MenuPersonnalise;
use App\Entity\MenuPersonnaliseLigne;
use App\Entity\Panier;
use App\Repository\CategorieRepository;
use App\Repository\LignePanierMenuRepository;
use App\Repository\MenuPersonnaliseRepository;
use App\Repository\OptionRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuPersonnaliseController extends AbstractController
{
    private function getSessionId(Request $req): string
    {
        $session = $req->getSession();
        $token   = $session->get('_menu_perso_token');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            $session->set('_menu_perso_token', $token);
        }
        return $token;
    }

    private function getPanier(Request $req, PanierRepository $repo, EntityManagerInterface $em): Panier
    {
        $session  = $req->getSession();
        $panierId = $session->get('panier_id');

        if ($panierId) {
            $panier = $repo->find($panierId);
            if ($panier) {
                return $panier;
            }
        }

        $panier = new Panier();
        $panier->setSessionId(uniqid('panier_', true));
        $em->persist($panier);
        $em->flush();
        $session->set('panier_id', $panier->getId());

        return $panier;
    }

    /** Page "Créer mon menu" : affiche toutes les catégories avec leurs produits */
    #[Route('/mon-menu/creer', name: 'app_menu_perso_creer')]
    public function creer(CategorieRepository $categorieRepo): Response
    {
        $categories = $categorieRepo->findBy([], ['position' => 'ASC']);

        return $this->render('menu_personnalise/creer.html.twig', [
            'categories' => $categories,
        ]);
    }

    /** Sauvegarde le menu personnalisé (en session) */
    #[Route('/mon-menu/sauvegarder', name: 'app_menu_perso_sauvegarder', methods: ['POST'])]
    public function sauvegarder(
        Request $req,
        CategorieRepository $categorieRepo,
        ProduitRepository $produitRepo,
        OptionRepository $optionRepo,
        EntityManagerInterface $em
    ): Response {
        $sessionId = $this->getSessionId($req);
        $nom       = trim($req->request->get('nom_menu', '')) ?: null;

        $menu = new MenuPersonnalise();
        $menu->setSessionId($sessionId);
        $menu->setNom($nom);

        $categories = $categorieRepo->findBy([], ['position' => 'ASC']);

        foreach ($categories as $categorie) {
            $key       = 'cat_' . $categorie->getId() . '_produit';
            $optKey    = 'cat_' . $categorie->getId() . '_options';
            $produitId = $req->request->get($key);

            if (!$produitId) {
                continue;
            }

            $produit = $produitRepo->find($produitId);
            if (!$produit) {
                continue;
            }

            $ligne = new MenuPersonnaliseLigne();
            $ligne->setProduit($produit);

            foreach ($req->request->all($optKey) ?? [] as $optId) {
                $option = $optionRepo->find($optId);
                if ($option) {
                    $ligne->addOption($option);
                }
            }

            $menu->addLigne($ligne);
        }

        if ($menu->getLignes()->isEmpty()) {
            $this->addFlash('error', 'Sélectionnez au moins un produit.');
            return $this->redirectToRoute('app_menu_perso_creer');
        }

        $em->persist($menu);
        $em->flush();

        $this->addFlash('success', 'Menu enregistré !');
        return $this->redirectToRoute('app_menu_perso_liste');
    }

    /** Liste les menus personnalisés de la session */
    #[Route('/mon-menu', name: 'app_menu_perso_liste')]
    public function liste(Request $req, MenuPersonnaliseRepository $repo): Response
    {
        $menus = $repo->findBySession($this->getSessionId($req));

        return $this->render('menu_personnalise/liste.html.twig', [
            'menus' => $menus,
        ]);
    }

    /** Supprime un menu personnalisé */
    #[Route('/mon-menu/supprimer/{id}', name: 'app_menu_perso_supprimer', methods: ['POST'])]
    public function supprimer(
        int $id,
        Request $req,
        MenuPersonnaliseRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $menu = $repo->find($id);

        if ($menu && $menu->getSessionId() === $this->getSessionId($req)) {
            $em->remove($menu);
            $em->flush();
            $this->addFlash('success', 'Menu supprimé.');
        }

        return $this->redirectToRoute('app_menu_perso_liste');
    }

    /** Ajoute un menu personnalisé au panier */
    #[Route('/mon-menu/ajouter-au-panier/{id}', name: 'app_menu_perso_ajouter_panier', methods: ['POST'])]
    public function ajouterAuPanier(
        int $id,
        Request $req,
        MenuPersonnaliseRepository $menuRepo,
        PanierRepository $panierRepo,
        EntityManagerInterface $em
    ): Response {
        $menu = $menuRepo->find($id);

        if (!$menu) {
            $this->addFlash('error', 'Menu introuvable.');
            return $this->redirectToRoute('app_menu_perso_liste');
        }

        $quantite = max(1, (int) $req->request->get('quantite', 1));
        $panier   = $this->getPanier($req, $panierRepo, $em);

        // Convertir en LignePanierMenu (sans modèle de menu prédéfini)
        $ligne = new LignePanierMenu();
        $ligne->setQuantite($quantite);
        $ligne->setPrixUnitaire((string) $menu->getPrixTotal());
        $ligne->setPanier($panier);
        // menu_id est nullable pour les menus perso (pas de modèle prédéfini)

        foreach ($menu->getLignes() as $lignePerso) {
            $detail = new LignePanierMenuDetail();
            $detail->setProduit($lignePerso->getProduit());
            // composant_id nullable pour les menus perso
            foreach ($lignePerso->getOptions() as $option) {
                $detail->addOption($option);
            }
            $ligne->addDetail($detail);
        }

        $panier->addLigneMenu($ligne);
        $em->persist($ligne);
        $em->flush();

        $this->addFlash('success', 'Menu ajouté au panier !');
        return $this->redirectToRoute('app_panier_index');
    }
}
