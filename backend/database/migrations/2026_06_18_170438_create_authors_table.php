<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('authors', function (Blueprint $blueprint) {
            $blueprint->id(); 
            $blueprint->string('first_name'); 
            $blueprint->string('last_name'); 
            $blueprint->text('biography')->nullable(); 
            $blueprint->string('nationality')->nullable(); 
            $blueprint->date('birth_date')->nullable(); 
            $blueprint->date('death_date')->nullable(); 
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
