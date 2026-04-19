<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class CommandeController extends AbstractController
{
    // Valide le panier et crée la commande en BDD
    #[Route('/commande/valider', name: 'app_commande_valider', methods: ['POST'])]
    public function valider(
        Request $req,
        PanierRepository $panierRepo,
        EntityManagerInterface $em
    ): Response {
        $sessionId = $req->getSession()->getId();
        $panier    = $panierRepo->findOneBy(['sessionId' => $sessionId]);

        if (!$panier || $panier->getLignes()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setNumero('KB-' . date('Ymd') . rand(100, 999));
        $commande->setStatut('en_attente');
        $commande->setCommentaire($req->request->get('commentaire'));

        $total = 0;

        // Transformation des lignes du panier en lignes de commande
        foreach ($panier->getLignes() as $lignePanier) {
            $ligneCmd = new LigneCommande();
            $ligneCmd->setProduit($lignePanier->getProduit());
            $ligneCmd->setNomProduit($lignePanier->getProduit()->getNom());
            $ligneCmd->setQuantite($lignePanier->getQuantite());
            $ligneCmd->setPrixUnitaire($lignePanier->getPrixUnitaire());

            // Snapshot des options en JSON
            $snapshot = [];
            foreach ($lignePanier->getOptions() as $opt) {
                $snapshot[] = [
                    'nom'  => $opt->getNom(),
                    'prix' => $opt->getPrixSupplementaire(),
                ];
            }
            $ligneCmd->setOptionsChoisies($snapshot);

            $commande->addLigne($ligneCmd);
            $total += (float) $lignePanier->getPrixUnitaire() * $lignePanier->getQuantite();
        }

        $commande->setTotal((string) $total);
        $em->persist($commande);

        // Vider le panier après validation
        foreach ($panier->getLignes() as $ligne) {
            $em->remove($ligne);
        }
        $em->flush();

        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    // Affiche la page de confirmation
    #[Route('/commande/confirmation/{id}', name: 'app_commande_confirmation')]
    public function confirmation(int $id, CommandeRepository $commandeRepo): Response
    {
        $commande = $commandeRepo->find($id);
        return $this->render('commande/confirmation.html.twig', ['commande' => $commande]);
    }
}