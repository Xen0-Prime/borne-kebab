<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarteController extends AbstractController
{
    // Route page d'accueil : affiche toutes les catégories
    #[Route('/', name: 'app_carte_index')]
    public function index(CategorieRepository $categorieRepo): Response
    {
        $categories = $categorieRepo->findBy([], ['position' => 'ASC']);

        return $this->render('carte/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Route catégorie : affiche les produits d'une catégorie
    #[Route('/categorie/{id}', name: 'app_carte_categorie')]
    public function categorie(
        int $id,
        CategorieRepository $categorieRepo,
        ProduitRepository $produitRepo
    ): Response {
        $categorie = $categorieRepo->find($id);
        $produits  = $produitRepo->findBy(
            ['categorie' => $categorie, 'disponible' => true],
            ['position' => 'ASC']
        );

        return $this->render('carte/categorie.html.twig', [
            'categorie' => $categorie,
            'produits'  => $produits,
        ]);
    }
}