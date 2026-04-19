<?php
namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProduitController extends AbstractController
{
    // Affiche le détail d'un produit + ses options disponibles
    #[Route('/produit/{id}', name: 'app_produit_detail')]
    public function detail(int $id, ProduitRepository $produitRepo): Response
    {
        $produit = $produitRepo->find($id);

        // Sépare les options en deux groupes pour l'affichage
        $optionsAjout   = [];
        $optionsRetrait = [];

        foreach ($produit->getOptions() as $option) {
            if ($option->getType() === 'ajout') {
                $optionsAjout[] = $option;
            } else {
                $optionsRetrait[] = $option;
            }
        }

        return $this->render('produit/detail.html.twig', [
            'produit'        => $produit,
            'optionsAjout'   => $optionsAjout,
            'optionsRetrait' => $optionsRetrait,
        ]);
    }
}