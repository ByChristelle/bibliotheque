<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Sciences', 'slug' => 'sciences', 'description' => 'Références scientifiques et techniques', 'status' => 'active'],
            ['name' => 'Littérature', 'slug' => 'litterature', 'description' => 'Oeuvres littéraires (romans, poèmes, etc.)', 'status' => 'active'],
            ['name' => 'Droit', 'slug' => 'droit', 'description' => 'Références juridiques et législatives', 'status' => 'active'],
            ['name' => 'Histoire', 'slug' => 'histoire', 'description' => 'Ouvrages historiques et géopolitiques', 'status' => 'active'],
            ['name' => 'Philosophie', 'slug' => 'philosophie', 'description' => 'Textes philosophiques et théoriques', 'status' => 'active'],
            ['name' => 'Économie', 'slug' => 'economie', 'description' => 'Références en économie et gestion', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
