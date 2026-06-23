<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $publishers = [
            ['name' => 'MIT Press', 'description' => 'Éditeur universitaire américain', 'country' => 'États-Unis', 'website' => 'https://mitpress.mit.edu'],
            ['name' => 'Gallimard', 'description' => 'Éditeur français indépendant', 'country' => 'France', 'website' => 'https://www.gallimard.fr'],
            ['name' => 'Presses Universitaires de France', 'description' => 'Éditeur universitaire français', 'country' => 'France', 'website' => 'https://www.puf.com'],
            ['name' => 'Oxford University Press', 'description' => 'Éditeur universitaire britannique', 'country' => 'Royaume-Uni', 'website' => 'https://global.oup.com'],
        ];

        foreach ($publishers as $publisher) {
            Publisher::create($publisher);
        }
    }
}
