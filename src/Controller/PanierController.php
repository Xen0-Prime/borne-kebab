<?php
namespace App\Controller;

use App\Entity\LignePanier;
use App\Entity\Panier;
use App\Repository\CategorieRepository;
use App\Repository\LignePanierRepository;
use App\Repository\OptionRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{
    // Récupère ou crée le panier lié à la session
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

    // Affiche le contenu du panier
    #[Route('/panier', name: 'app_panier_index')]
    public function index(Request $req, PanierRepository $repo, EntityManagerInterface $em, CategorieRepository $categorieRepo): Response
    {
        $panier = $this->getPanier($req, $repo, $em);
        $categories = $categorieRepo->findBy([], ['position' => 'ASC']);
        return $this->render('panier/index.html.twig', ['panier' => $panier, 'categories' => $categories]);
    }

    // Ajoute un produit (avec ses options) au panier
    #[Route('/panier/ajouter', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(
        Request $req,
        PanierRepository $panierRepo,
        ProduitRepository $produitRepo,
        OptionRepository $optionRepo,
        EntityManagerInterface $em
    ): Response {
        $panier    = $this->getPanier($req, $panierRepo, $em);
        $produit   = $produitRepo->find($req->request->get('produit_id'));
        $optionIds = $req->request->all('options') ?? [];

        // Calcul du prix : base + suppléments des options choisies
        $prix = (float) $produit->getPrix();
        $ligne = new LignePanier();
        $ligne->setProduit($produit);
        $ligne->setQuantite(1);

        foreach ($optionIds as $optId) {
            $option = $optionRepo->find($optId);
            if ($option) {
                $ligne->addOption($option);
                $prix += (float) $option->getPrixSupplementaire();
            }
        }

        $ligne->setPrixUnitaire((string) $prix);
        $panier->addLigne($ligne);
        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('app_panier_index');
    }

    // Modifie la quantité d'une ligne
    #[Route('/panier/modifier/{id}', name: 'app_panier_modifier', methods: ['POST'])]
    public function modifier(
        int $id,
        Request $req,
        LignePanierRepository $ligneRepo,
        EntityManagerInterface $em
    ): Response {
        $ligne    = $ligneRepo->find($id);
        $quantite = (int) $req->request->get('quantite');

        if ($quantite < 1) {
            $em->remove($ligne);
        } else {
            $ligne->setQuantite($quantite);
        }
        $em->flush();

        return $this->redirectToRoute('app_panier_index');
    }

    // Supprime une ligne du panier
    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer', methods: ['POST'])]
    public function supprimer(int $id, LignePanierRepository $ligneRepo, EntityManagerInterface $em): Response
    {
        $ligne = $ligneRepo->find($id);
        $em->remove($ligne);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé du panier.');
        return $this->redirectToRoute('app_panier_index');
    }
}