<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Menu;
use App\Entity\MenuComposant;
use App\Entity\Option;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // =============================================
        // 1. CRÉATION DES OPTIONS / INGRÉDIENTS (8)
        // =============================================
        // On crée les options EN PREMIER pour pouvoir les lier aux produits ensuite

        $optionsData = [
            // --- GRATUITES (type retrait) ---
            [
                'nom'   => 'Sans oignons',
                'desc'  => 'Retirer les oignons',
                'prix'  => '0.00',
                'type'  => 'retrait',
            ],
            [
                'nom'   => 'Sans cornichons',
                'desc'  => 'Retirer les cornichons',
                'prix'  => '0.00',
                'type'  => 'retrait',
            ],
            [
                'nom'   => 'Sans tomates',
                'desc'  => 'Retirer les tomates',
                'prix'  => '0.00',
                'type'  => 'retrait',
            ],
            // --- GRATUITES (type ajout) ---
            [
                'nom'   => 'Sauce blanche',
                'desc'  => 'Ajouter de la sauce blanche',
                'prix'  => '0.00',
                'type'  => 'ajout',
            ],
            [
                'nom'   => 'Sauce harissa',
                'desc'  => 'Ajouter de la sauce harissa',
                'prix'  => '0.00',
                'type'  => 'ajout',
            ],
            [
                'nom'   => 'Salade supplémentaire',
                'desc'  => 'Portion de salade en plus',
                'prix'  => '0.00',
                'type'  => 'ajout',
            ],
            // --- PAYANTES (type ajout) ---
            [
                'nom'   => 'Fromage fondu',
                'desc'  => 'Fromage fondu gratiné',
                'prix'  => '1.00',
                'type'  => 'ajout',
            ],
            [
                'nom'   => 'Bacon grillé',
                'desc'  => 'Tranches de bacon croustillant',
                'prix'  => '1.50',
                'type'  => 'ajout',
            ],
        ];

        // On stocke les objets Option dans un tableau pour les réutiliser
        $options = [];
        foreach ($optionsData as $data) {
            $option = new Option();
            $option->setNom($data['nom']);
            $option->setDescription($data['desc']);
            $option->setPrixSupplementaire($data['prix']);
            $option->setType($data['type']);
            $option->setDisponible(true);
            $manager->persist($option);
            $options[] = $option;
        }

        // =============================================
        // 2. CRÉATION DES CATÉGORIES (3)
        // =============================================

        $categoriesData = [
            [
                'nom'      => 'Kebabs',
                'desc'     => 'Nos kebabs maison préparés à la commande',
                'position' => 1,
            ],
            [
                'nom'      => 'Sandwichs',
                'desc'     => 'Sandwichs chauds et froids',
                'position' => 2,
            ],
            [
                'nom'      => 'Boissons & Extras',
                'desc'     => 'Boissons fraîches et accompagnements',
                'position' => 3,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categorie = new Categorie();
            $categorie->setNom($data['nom']);
            $categorie->setDescription($data['desc']);
            $categorie->setPosition($data['position']);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // =============================================
        // 3. CRÉATION DES PRODUITS (12 — 4 par catégorie)
        // =============================================
        // Pour chaque produit, on précise quelles options sont disponibles
        // en utilisant les index du tableau $options (0 à 7)

        $produitsData = [

            // --- Catégorie 0 : Kebabs ---
            [
                'categorie' => 0,
                'nom'       => 'Kebab Classique',
                'desc'      => 'Viande de veau grillée, salade, tomates, oignons, sauce blanche',
                'prix'      => '6.50',
                'position'  => 1,
                'options'   => [0, 1, 2, 3, 4, 5, 6, 7], // toutes les options
            ],
            [
                'categorie' => 0,
                'nom'       => 'Kebab Double Viande',
                'desc'      => 'Double portion de viande grillée avec tous les accompagnements',
                'prix'      => '8.50',
                'position'  => 2,
                'options'   => [0, 1, 2, 3, 4, 5, 6, 7],
            ],
            [
                'categorie' => 0,
                'nom'       => 'Kebab Poulet',
                'desc'      => 'Poulet mariné grillé, salade fraîche, sauce au choix',
                'prix'      => '7.00',
                'position'  => 3,
                'options'   => [0, 1, 2, 3, 4, 5, 6, 7],
            ],
            [
                'categorie' => 0,
                'nom'       => 'Kebab Mixte',
                'desc'      => 'Mélange veau et poulet, légumes frais, double sauce',
                'prix'      => '8.00',
                'position'  => 4,
                'options'   => [0, 1, 2, 3, 4, 5, 6, 7],
            ],

            // --- Catégorie 1 : Sandwichs ---
            [
                'categorie' => 1,
                'nom'       => 'Sandwich Grec',
                'desc'      => 'Pain pita, viande grillée, tzatziki, tomates, oignons rouges',
                'prix'      => '6.00',
                'position'  => 1,
                'options'   => [0, 2, 3, 4, 6, 7], // sans cornichons (pas logique), avec fromage/bacon
            ],
            [
                'categorie' => 1,
                'nom'       => 'Sandwich Falafel',
                'desc'      => 'Falafels maison, houmous, salade, sauce tahini',
                'prix'      => '5.50',
                'position'  => 2,
                'options'   => [0, 1, 2, 3, 4, 5], // pas de bacon sur falafel
            ],
            [
                'categorie' => 1,
                'nom'       => 'Sandwich Merguez',
                'desc'      => 'Merguez grillées, frites, harissa, moutarde',
                'prix'      => '6.50',
                'position'  => 3,
                'options'   => [0, 2, 3, 4, 5, 7],
            ],
            [
                'categorie' => 1,
                'nom'       => 'Sandwich Thon',
                'desc'      => 'Thon, maïs, tomates, oeufs durs, sauce mayo',
                'prix'      => '5.00',
                'position'  => 4,
                'options'   => [0, 1, 2, 3, 5, 6],
            ],

            // --- Catégorie 2 : Boissons & Extras ---
            [
                'categorie' => 2,
                'nom'       => 'Frites Maison',
                'desc'      => 'Frites fraîches croustillantes, sel, ketchup',
                'prix'      => '2.50',
                'position'  => 1,
                'options'   => [], // pas d'options sur les frites
            ],
            [
                'categorie' => 2,
                'nom'       => 'Boisson 33cl',
                'desc'      => 'Coca-Cola, Fanta, Sprite, eau minérale au choix',
                'prix'      => '2.00',
                'position'  => 2,
                'options'   => [],
            ],
            [
                'categorie' => 2,
                'nom'       => 'Salade Fraîche',
                'desc'      => 'Salade verte, tomates cerises, concombre, vinaigrette',
                'prix'      => '3.00',
                'position'  => 3,
                'options'   => [],
            ],
            [
                'categorie' => 2,
                'nom'       => 'Baklava',
                'desc'      => 'Pâtisserie orientale au miel et aux pistaches',
                'prix'      => '2.50',
                'position'  => 4,
                'options'   => [],
            ],
        ];

        foreach ($produitsData as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['desc']);
            $produit->setPrix($data['prix']);
            $produit->setDisponible(true);
            $produit->setPosition($data['position']);
            $produit->setCategorie($categories[$data['categorie']]);

            // Liaison des options disponibles pour ce produit
            foreach ($data['options'] as $optionIndex) {
                $produit->addOption($options[$optionIndex]);
            }

            $manager->persist($produit);
        }

        // =============================================
        // 4. CRÉATION DES MENUS BASIQUES (3)
        // =============================================
        // categories: 0=Kebabs, 1=Sandwichs, 2=Boissons & Extras

        $menusData = [
            [
                'nom'         => 'Menu Kebab',
                'description' => 'Un kebab au choix + une boisson au choix',
                'prix'        => '8.50',
                'position'    => 1,
                'composants'  => [
                    ['nom' => 'Kebab au choix',   'categorie' => 0, 'position' => 1],
                    ['nom' => 'Boisson au choix', 'categorie' => 2, 'position' => 2],
                ],
            ],
            [
                'nom'         => 'Menu Sandwich',
                'description' => 'Un sandwich au choix + une boisson au choix',
                'prix'        => '7.50',
                'position'    => 2,
                'composants'  => [
                    ['nom' => 'Sandwich au choix', 'categorie' => 1, 'position' => 1],
                    ['nom' => 'Boisson au choix',  'categorie' => 2, 'position' => 2],
                ],
            ],
            [
                'nom'         => 'Menu Complet',
                'description' => 'Un kebab au choix + un accompagnement + une boisson',
                'prix'        => '12.00',
                'position'    => 3,
                'composants'  => [
                    ['nom' => 'Kebab au choix',      'categorie' => 0, 'position' => 1],
                    ['nom' => 'Accompagnement',      'categorie' => 2, 'position' => 2],
                    ['nom' => 'Boisson au choix',    'categorie' => 2, 'position' => 3],
                ],
            ],
        ];

        foreach ($menusData as $menuData) {
            $menu = new Menu();
            $menu->setNom($menuData['nom']);
            $menu->setDescription($menuData['description']);
            $menu->setPrix($menuData['prix']);
            $menu->setDisponible(true);
            $menu->setPosition($menuData['position']);

            foreach ($menuData['composants'] as $compData) {
                $composant = new MenuComposant();
                $composant->setNom($compData['nom']);
                $composant->setPosition($compData['position']);
                $composant->setCategorie($categories[$compData['categorie']]);
                $menu->addComposant($composant);
                $manager->persist($composant);
            }

            $manager->persist($menu);
        }

        // =============================================
        // 5. ENVOI EN BASE DE DONNÉES
        // =============================================
        $manager->flush();
    }
}