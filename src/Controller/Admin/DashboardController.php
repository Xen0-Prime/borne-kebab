<?php

namespace App\Controller\Admin;

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
        return $this->redirectToRoute('admin_categorie_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Borne Kebab');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Carte');
        yield MenuItem::linkTo(CategorieCrudController::class, 'Catégories', 'fas fa-tags');
        yield MenuItem::linkTo(ProduitCrudController::class, 'Produits', 'fas fa-burger');
        yield MenuItem::linkTo(OptionCrudController::class, 'Options', 'fas fa-sliders');

        yield MenuItem::section('Menus');
        yield MenuItem::linkTo(MenuCrudController::class, 'Menus', 'fas fa-utensils');
        yield MenuItem::linkTo(MenuComposantCrudController::class, 'Composants de menu', 'fas fa-layer-group');

        yield MenuItem::section('Commandes & Paniers');
        yield MenuItem::linkTo(CommandeCrudController::class, 'Commandes', 'fas fa-receipt');
        yield MenuItem::linkTo(LigneCommandeCrudController::class, 'Lignes de commande', 'fas fa-list');
        yield MenuItem::linkTo(PanierCrudController::class, 'Paniers', 'fas fa-shopping-cart');
        yield MenuItem::linkTo(LignePanierCrudController::class, 'Lignes de panier', 'fas fa-list-ul');
    }
}
