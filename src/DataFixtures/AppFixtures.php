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
        // 1. OPTIONS
        $optionsData = [
            ['nom' => 'Sans chou',            'desc' => 'Retirer le chou',                          'prix' => '0.00', 'type' => 'retrait'],
            ['nom' => 'Sans sauce tonkatsu',  'desc' => 'Retirer la sauce tonkatsu',                'prix' => '0.00', 'type' => 'retrait'],
            ['nom' => 'Sans moutarde',        'desc' => 'Retirer la moutarde',                      'prix' => '0.00', 'type' => 'retrait'],
            ['nom' => 'Sans fromage',         'desc' => 'Retirer le fromage',                       'prix' => '0.00', 'type' => 'retrait'],
            ['nom' => 'Sauce tonkatsu extra', 'desc' => 'Portion supplementaire de sauce tonkatsu', 'prix' => '0.00', 'type' => 'ajout'],
            ['nom' => 'Sauce mayo',           'desc' => 'Mayo japonaise Kewpie',                    'prix' => '0.00', 'type' => 'ajout'],
            ['nom' => 'Chou extra',           'desc' => 'Portion de chou supplementaire',           'prix' => '0.00', 'type' => 'ajout'],
            ['nom' => 'Fromage fondu',        'desc' => 'Fromage japonais fondu',                   'prix' => '1.00', 'type' => 'ajout'],
            ['nom' => 'Oeuf au plat',         'desc' => 'Oeuf au plat croustillant',                'prix' => '1.00', 'type' => 'ajout'],
            ['nom' => 'Double viande',        'desc' => 'Portion de viande doublee',                'prix' => '2.50', 'type' => 'ajout'],
        ];
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

        // 2. CATEGORIES
        $categoriesData = [
            ['nom' => 'Sandwichs Katsu', 'desc' => 'Nos sandwichs katsu maison, panes a la commande', 'position' => 1],
            ['nom' => 'Accompagnements', 'desc' => 'Frites, coleslaw et a-cotes',                      'position' => 2],
            ['nom' => 'Boissons',        'desc' => 'Boissons japonaises et classiques',                'position' => 3],
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

        // 3. PRODUITS
        $produitsData = [
            ['categorie' => 0, 'nom' => "Yoshikage's Katsu",    'desc' => 'Porc pane, chou emince, sauce tonkatsu, pain brioche grille',  'prix' => '7.50',  'position' => 1, 'options' => [0,1,2,3,4,5,6,7,8,9]],
            ['categorie' => 0, 'nom' => "Okuyasu's Double Katsu",'desc' => 'Double escalope de porc pane, chou, mayo Kewpie',              'prix' => '10.50', 'position' => 2, 'options' => [0,1,2,3,4,5,6,7,8]],
            ['categorie' => 0, 'nom' => "Koichi's Chicken Katsu",'desc' => 'Poulet marine au sake, pane panko, sauce gingembre yuzu',      'prix' => '7.90',  'position' => 3, 'options' => [0,1,2,3,4,5,6,7,8,9]],
            ['categorie' => 0, 'nom' => "Yukako's Spicy Katsu",  'desc' => 'Porc pane, sauce tonkatsu pimentee, cornichons tsukemono',     'prix' => '8.00',  'position' => 4, 'options' => [0,1,2,3,4,5,6,7,8,9]],
            ['categorie' => 0, 'nom' => "Rohan's Tofu Katsu",    'desc' => 'Tofu ferme pane panko, sauce miso-sesame, chou - vegetarien',  'prix' => '6.90',  'position' => 5, 'options' => [0,1,2,3,4,5,6,7,8]],
            ['categorie' => 0, 'nom' => "Josuke's Katsu Royal",  'desc' => 'Porc pane, fromage fondu, sauce BBQ japonaise, chou caramelise','prix' => '9.00', 'position' => 6, 'options' => [0,1,2,4,5,6,8,9]],
            ['categorie' => 1, 'nom' => 'Frites Maison',           'desc' => 'Frites fraiches croustillantes, sel de mer, ketchup maison',      'prix' => '3.00', 'position' => 1, 'options' => []],
            ['categorie' => 1, 'nom' => 'Frites Dorees au Sesame', 'desc' => 'Frites assaisonnees sesame torrefie, piment shichimi, sauce mayo', 'prix' => '3.50', 'position' => 2, 'options' => []],
            ['categorie' => 1, 'nom' => 'Coleslaw Japonais',       'desc' => 'Chou emince, carottes, mayo Kewpie, vinaigre de riz',              'prix' => '2.50', 'position' => 3, 'options' => []],
            ['categorie' => 1, 'nom' => 'Soupe Miso',              'desc' => 'Soupe miso, tofu, algues wakame, oignons verts',                   'prix' => '2.00', 'position' => 4, 'options' => []],
            ['categorie' => 2, 'nom' => 'Ramune Original', 'desc' => 'Limonade japonaise a la bille - saveur originale', 'prix' => '3.00', 'position' => 1, 'options' => []],
            ['categorie' => 2, 'nom' => 'Ramune Fraise',   'desc' => 'Limonade japonaise a la bille - saveur fraise',    'prix' => '3.00', 'position' => 2, 'options' => []],
            ['categorie' => 2, 'nom' => 'The Vert Glace',  'desc' => 'The vert sencha infuse a froid, legerement sucre', 'prix' => '2.50', 'position' => 3, 'options' => []],
            ['categorie' => 2, 'nom' => 'Coca-Cola 33cl',  'desc' => 'Coca-Cola Original en canette bien fraiche',       'prix' => '2.00', 'position' => 4, 'options' => []],
        ];
        foreach ($produitsData as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['desc']);
            $produit->setPrix($data['prix']);
            $produit->setDisponible(true);
            $produit->setPosition($data['position']);
            $produit->setCategorie($categories[$data['categorie']]);
            foreach ($data['options'] as $idx) {
                $produit->addOption($options[$idx]);
            }
            $manager->persist($produit);
        }

        // 4. MENUS
        $menusData = [
            [
                'nom' => 'Menu Gentleman Simple',
                'description' => 'Un sandwich katsu au choix + une boisson au choix',
                'prix' => '9.50', 'position' => 1,
                'composants' => [
                    ['nom' => 'Katsu au choix',   'categorie' => 0, 'position' => 1],
                    ['nom' => 'Boisson au choix', 'categorie' => 2, 'position' => 2],
                ],
            ],
            [
                'nom' => 'Menu Gentleman Complet',
                'description' => 'Un sandwich katsu + un accompagnement + une boisson',
                'prix' => '12.50', 'position' => 2,
                'composants' => [
                    ['nom' => 'Katsu au choix',          'categorie' => 0, 'position' => 1],
                    ['nom' => 'Accompagnement au choix', 'categorie' => 1, 'position' => 2],
                    ['nom' => 'Boisson au choix',        'categorie' => 2, 'position' => 3],
                ],
            ],
            [
                'nom' => 'Menu Killer Queen',
                'description' => 'Deux sandwichs katsu + deux accompagnements + une boisson',
                'prix' => '22.00', 'position' => 3,
                'composants' => [
                    ['nom' => 'Premier katsu',           'categorie' => 0, 'position' => 1],
                    ['nom' => 'Deuxieme katsu',          'categorie' => 0, 'position' => 2],
                    ['nom' => 'Premier accompagnement',  'categorie' => 1, 'position' => 3],
                    ['nom' => 'Deuxieme accompagnement', 'categorie' => 1, 'position' => 4],
                    ['nom' => 'Boisson au choix',        'categorie' => 2, 'position' => 5],
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

        // 5. FLUSH
        $manager->flush();
    }
}