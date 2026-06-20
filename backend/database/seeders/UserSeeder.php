<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrateur
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Bibliothèque',
            'email' => 'admin@biblio.bj',
            'role' => 'admin',
        ]);

        // Responsable RH
        User::factory()->create([
            'first_name' => 'Jean',
            'last_name' => 'RH',
            'email' => 'rh@biblio.bj',
            'role' => 'responsable_rh',
        ]);

        // Responsable des Demandes
        User::factory()->create([
            'first_name' => 'Marc',
            'last_name' => 'Demande',
            'email' => 'responsable@biblio.bj',
            'role' => 'responsable_demande',
        ]);

        // Utilisateur inscrit standard
        User::factory()->create([
            'first_name' => 'Ablavi',
            'last_name' => 'Koffi',
            'email' => 'user@biblio.bj',
            'role' => 'user',
        ]);

        // Génération de 10 utilisateurs aléatoires
        User::factory(10)->create();
    }
}