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
      Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('abstract')->nullable();
            $table->string('isbn')->nullable();
            $table->year('publication_year')->nullable();
            $table->enum('language', ['fr', 'en', 'autre'])->default('fr');
            $table->enum('document_type', ['livre', 'memoire', 'these', 'article', 'revue', 'rapport', 'guide', 'autre']);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('cover_image')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('pages')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
