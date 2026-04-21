<?php

namespace App\Controller;

use App\Entity\LignePanierMenu;
use App\Entity\LignePanierMenuDetail;
use App\Entity\Panier;
use App\Repository\MenuComposantRepository;
use App\Repository\MenuRepository;
use App\Repository\OptionRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
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

    /** Liste tous les menus disponibles */
    #[Route('/menus', name: 'app_menu_index')]
    public function index(MenuRepository $menuRepo): Response
    {
        $menus = $menuRepo->findBy(['disponible' => true], ['position' => 'ASC']);

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    /** Affiche le détail d'un menu et le formulaire de configuration */
    #[Route('/menu/{id}', name: 'app_menu_configurer')]
    public function configurer(int $id, MenuRepository $menuRepo): Response
    {
        $menu = $menuRepo->find($id);

        if (!$menu || !$menu->isDisponible()) {
            throw $this->createNotFoundException('Menu introuvable.');
        }

        return $this->render('menu/configurer.html.twig', [
            'menu' => $menu,
        ]);
    }

    /** Ajoute un menu configuré au panier */
    #[Route('/panier/ajouter-menu', name: 'app_panier_ajouter_menu', methods: ['POST'])]
    public function ajouterMenu(
        Request $req,
        PanierRepository $panierRepo,
        MenuRepository $menuRepo,
        MenuComposantRepository $composantRepo,
        ProduitRepository $produitRepo,
        OptionRepository $optionRepo,
        EntityManagerInterface $em
    ): Response {
        $panier = $this->getPanier($req, $panierRepo, $em);
        $menu   = $menuRepo->find($req->request->get('menu_id'));

        if (!$menu) {
            $this->addFlash('error', 'Menu introuvable.');
            return $this->redirectToRoute('app_menu_index');
        }

        // Calcul du prix total : prix de base du menu + suppléments options
        $prixTotal = (float) $menu->getPrix();

        $lignePanierMenu = new LignePanierMenu();
        $lignePanierMenu->setMenu($menu);
        $lignePanierMenu->setQuantite(max(1, (int) $req->request->get('quantite', 1)));

        // Pour chaque composant : récupérer le produit choisi et les options
        foreach ($menu->getComposants() as $composant) {
            $key       = 'composant_' . $composant->getId();
            $produitId = $req->request->get($key . '_produit');
            $optionIds = $req->request->all($key . '_options') ?? [];

            if (!$produitId) {
                continue;
            }

            $produit = $produitRepo->find($produitId);
            if (!$produit) {
                continue;
            }

            $detail = new LignePanierMenuDetail();
            $detail->setComposant($composant);
            $detail->setProduit($produit);

            foreach ($optionIds as $optId) {
                $option = $optionRepo->find($optId);
                if ($option) {
                    $detail->addOption($option);
                    $prixTotal += (float) $option->getPrixSupplementaire();
                }
            }

            $lignePanierMenu->addDetail($detail);
        }

        $lignePanierMenu->setPrixUnitaire((string) $prixTotal);
        $panier->addLigneMenu($lignePanierMenu);
        $em->persist($lignePanierMenu);
        $em->flush();

        $this->addFlash('success', 'Menu ajouté au panier !');
        return $this->redirectToRoute('app_panier_index');
    }
}
