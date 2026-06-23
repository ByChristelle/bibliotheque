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
            [
                'title' => 'Introduction aux algorithmes',
                'subtitle' => 'Algorithmique fondamentale',
                'abstract' => 'Livre de référence sur l\'algorithmique',
                'isbn' => '978-2-7465-1234-5',
                'publication_year' => 2009,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1200,
                'status' => 'published'
            ],
            [
                'title' => 'Les Misérables',
                'subtitle' => 'Roman historique',
                'abstract' => 'Oeuvre majeure de Victor Hugo',
                'isbn' => '978-2-07-040845-6',
                'publication_year' => 1862,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1900,
                'status' => 'published'
            ],
            [
                'title' => 'Histoire de l\'Afrique',
                'subtitle' => 'Du Moyen Âge à nos jours',
                'abstract' => 'Histoire générale du continent africain',
                'isbn' => '978-2-84245-678-9',
                'publication_year' => 1972,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 800,
                'status' => 'published'
            ],
            [
                'title' => 'Du contrat social',
                'subtitle' => 'Principes du droit politique',
                'abstract' => 'Oeuvre fondamentale de Jean-Jacques Rousseau',
                'isbn' => '978-2-08123-456-7',
                'publication_year' => 1762,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 350,
                'status' => 'published'
            ],
            [
                'title' => 'La Richesse des nations',
                'subtitle' => 'Recherches sur la nature et les causes de la richesse des nations',
                'abstract' => 'Oeuvre fondatrice de l\'économie moderne',
                'isbn' => '978-2-07045-789-0',
                'publication_year' => 1776,
                'language' => 'fr',
                'document_type' => 'livre',
                'pages' => 1100,
                'status' => 'published'
            ]
        ];

        foreach ($references as $index => $refData) {
            // Créer la référence
            $reference = Reference::create([
                ...$refData,
                'category_id' => $categories[$index % $categories->count()]->id,
                'publisher_id' => $publishers[$index % $publishers->count()]->id,
                'uploaded_by' => $user->id,
            ]);

            // Attacher un auteur
            $reference->authors()->attach($authors[$index % $authors->count()]);
        }
    }
}
