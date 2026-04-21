<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('numero', 'Numéro'),
            MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(false),
            ChoiceField::new('statut', 'Statut')->setChoices([
                'En attente'    => 'en_attente',
                'En préparation' => 'en_preparation',
                'Prête'         => 'prete',
                'Livrée'        => 'livree',
            ]),
            DateTimeField::new('createdAt', 'Créée le')->hideOnForm(),
            TextareaField::new('commentaire', 'Commentaire')->hideOnIndex()->setRequired(false),
        ];
    }
}
