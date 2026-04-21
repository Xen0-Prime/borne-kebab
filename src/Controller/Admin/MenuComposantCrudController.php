<?php

namespace App\Controller\Admin;

use App\Entity\MenuComposant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuComposantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuComposant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('menu', 'Menu'),
            TextField::new('nom', 'Nom du composant'),
            IntegerField::new('position', 'Position'),
            AssociationField::new('categorie', 'Catégorie source')
                ->setHelp('Les produits disponibles pour ce slot viendront de cette catégorie'),
        ];
    }
}
