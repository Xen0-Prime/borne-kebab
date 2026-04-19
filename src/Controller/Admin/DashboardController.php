<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\LignePanier;
use App\Entity\Option;
use App\Entity\Panier;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Borne Kebab');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Categorie::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-burger', Produit::class);
        yield MenuItem::linkToCrud('Options', 'fas fa-sliders', Option::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-receipt', Commande::class);
        yield MenuItem::linkToCrud('Lignes de commande', 'fas fa-list', LigneCommande::class);
        yield MenuItem::linkToCrud('Paniers', 'fas fa-shopping-cart', Panier::class);
        yield MenuItem::linkToCrud('Lignes de panier', 'fas fa-list-ul', LignePanier::class);
    }
}
