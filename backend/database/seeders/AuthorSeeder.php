<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $authors = [
            ['first_name' => 'Thomas', 'last_name' => 'Cormen', 'biography' => 'Informaticien américain spécialisé en algorithmes', 'nationality' => 'États-Unis', 'birth_date' => '1956-01-01'],
            ['first_name' => 'Victor', 'last_name' => 'Hugo', 'biography' => 'Écrivain, poète et dramaturge français', 'nationality' => 'France', 'birth_date' => '1802-02-26', 'death_date' => '1885-05-22'],
            ['first_name' => 'Joseph', 'last_name' => 'Ki-Zerbo', 'biography' => 'Historien et homme politique burkinabé', 'nationality' => 'Burkina Faso', 'birth_date' => '1922-06-21', 'death_date' => '2006-12-04'],
            ['first_name' => 'Jean-Jacques', 'last_name' => 'Rousseau', 'biography' => 'Philosophe, écrivain et compositeur genevois', 'nationality' => 'Suisse', 'birth_date' => '1712-06-28', 'death_date' => '1778-07-02'],
            ['first_name' => 'Adam', 'last_name' => 'Smith', 'biography' => 'Philosophe et économiste écossais', 'nationality' => 'Royaume-Uni', 'birth_date' => '1723-06-05', 'death_date' => '1790-07-17'],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
