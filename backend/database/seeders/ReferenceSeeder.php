<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Récupérer les données nécessaires
        $user = User::first();
        $categories = Category::all();
        $publishers = Publisher::all();
        $authors = Author::all();

        $references = [
            // Catégorie 1: Sciences
            [
                'title' => 'Introduction aux algorithmes',
                'subtitle' => 'Algorithmique fondamentale',
                'abstract' => 'Livre de référence sur l\'algorithmique',
                'isbn' => '978-2-7465-1234-5',
                'publication_year' => 2009,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1200,
                'status' => 'published',
                'download_count' => 1240,
                'view_count' => 3500,
                'cover_image' => 'https://picsum.photos/seed/algorithmes/400/600.jpg',
                'category_id' => $categories[0]->id
            ],
            [
                'title' => 'Physique pour les débutants',
                'subtitle' => 'Mécanique et thermodynamique',
                'abstract' => 'Initiation à la physique classique',
                'isbn' => '978-2-1345-6789-0',
                'publication_year' => 2020,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 450,
                'status' => 'published',
                'download_count' => 850,
                'view_count' => 2100,
                'cover_image' => 'https://picsum.photos/seed/physique/400/600.jpg',
                'category_id' => $categories[0]->id
            ],
            // Catégorie 2: Littérature
            [
                'title' => 'Les Misérables',
                'subtitle' => 'Roman historique',
                'abstract' => 'Oeuvre majeure de Victor Hugo',
                'isbn' => '978-2-07-040845-6',
                'publication_year' => 1862,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1900,
                'status' => 'published',
                'download_count' => 5200,
                'view_count' => 12500,
                'cover_image' => 'https://picsum.photos/seed/miserables/400/600.jpg',
                'category_id' => $categories[1]->id
            ],
            [
                'title' => 'Notre-Dame de Paris',
                'subtitle' => 'Roman gothique',
                'abstract' => 'Histoire de Quasimodo et Esmeralda',
                'isbn' => '978-2-070-12345-6',
                'publication_year' => 1831,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 950,
                'status' => 'published',
                'download_count' => 3800,
                'view_count' => 9200,
                'cover_image' => 'https://picsum.photos/seed/notredame/400/600.jpg',
                'category_id' => $categories[1]->id
            ],
            // Catégorie 3: Droit
            [
                'title' => 'Code civil annoté',
                'subtitle' => 'Édition 2024',
                'abstract' => 'Code civil avec commentaires juridiques',
                'isbn' => '978-2-247-11223-3',
                'publication_year' => 2024,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 2500,
                'status' => 'published',
                'download_count' => 650,
                'view_count' => 1800,
                'cover_image' => 'https://picsum.photos/seed/civil/400/600.jpg',
                'category_id' => $categories[2]->id
            ],
            [
                'title' => 'Droit constitutionnel',
                'subtitle' => 'Institutions politiques',
                'abstract' => 'Étude du droit constitutionnel français',
                'isbn' => '978-2-345-67890-1',
                'publication_year' => 2023,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 850,
                'status' => 'published',
                'download_count' => 420,
                'view_count' => 1200,
                'cover_image' => 'https://picsum.photos/seed/constitutionnel/400/600.jpg',
                'category_id' => $categories[2]->id
            ],
            // Catégorie 4: Histoire
            [
                'title' => 'Histoire de l\'Afrique',
                'subtitle' => 'Du Moyen Âge à nos jours',
                'abstract' => 'Histoire générale du continent africain',
                'isbn' => '978-2-84245-678-9',
                'publication_year' => 1972,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 800,
                'status' => 'published',
                'download_count' => 980,
                'view_count' => 2700,
                'cover_image' => 'https://picsum.photos/seed/afrique/400/600.jpg',
                'category_id' => $categories[3]->id
            ],
            [
                'title' => 'Histoire du Bénin',
                'subtitle' => 'Des origines à l\'indépendance',
                'abstract' => 'Histoire du Bénin et du Dahomey',
                'isbn' => '978-2-9876-5432-1',
                'publication_year' => 2015,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 620,
                'status' => 'published',
                'download_count' => 1100,
                'view_count' => 3200,
                'cover_image' => 'https://picsum.photos/seed/benin/400/600.jpg',
                'category_id' => $categories[3]->id
            ],
            // Catégorie 5: Philosophie
            [
                'title' => 'Du contrat social',
                'subtitle' => 'Principes du droit politique',
                'abstract' => 'Oeuvre fondamentale de Jean-Jacques Rousseau',
                'isbn' => '978-2-08123-456-7',
                'publication_year' => 1762,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 350,
                'status' => 'published',
                'download_count' => 2100,
                'view_count' => 5800,
                'cover_image' => 'https://picsum.photos/seed/contrat/400/600.jpg',
                'category_id' => $categories[4]->id
            ],
            [
                'title' => 'Méditations',
                'subtitle' => 'Pensées de Marc Aurèle',
                'abstract' => 'Texte de philosophie stoïque',
                'isbn' => '978-2-1234-5678-9',
                'publication_year' => 180,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 280,
                'status' => 'published',
                'download_count' => 1500,
                'view_count' => 4100,
                'cover_image' => 'https://picsum.photos/seed/meditations/400/600.jpg',
                'category_id' => $categories[4]->id
            ],
            // Catégorie 6: Économie
            [
                'title' => 'La Richesse des nations',
                'subtitle' => 'Recherches sur la nature et les causes de la richesse des nations',
                'abstract' => 'Oeuvre fondatrice de l\'économie moderne',
                'isbn' => '978-2-07045-789-0',
                'publication_year' => 1776,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1100,
                'status' => 'published',
                'download_count' => 1800,
                'view_count' => 4900,
                'cover_image' => 'https://picsum.photos/seed/richesse/400/600.jpg',
                'category_id' => $categories[5]->id
            ],
            [
                'title' => 'Économie pour tous',
                'subtitle' => 'Comprendre l\'économie moderne',
                'abstract' => 'Initiation à l\'économie pour les non-spécialistes',
                'isbn' => '978-2-8765-4321-0',
                'publication_year' => 2022,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 520,
                'status' => 'published',
                'download_count' => 780,
                'view_count' => 2200,
                'cover_image' => 'https://picsum.photos/seed/economie/400/600.jpg',
                'category_id' => $categories[5]->id
            ]
        ];

        foreach ($references as $index => $refData) {
            $categoryId = $refData['category_id'];
            unset($refData['category_id']);

            // Créer la référence
            $reference = Reference::create([
                ...$refData,
                'category_id' => $categoryId,
                'publisher_id' => $publishers[$index % $publishers->count()]->id,
                'uploaded_by' => $user->id,
            ]);

            // Attacher un auteur
            $reference->authors()->attach($authors[$index % $authors->count()]);
        }
    }
}
