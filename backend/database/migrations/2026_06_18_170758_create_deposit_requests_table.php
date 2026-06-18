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
      Schema::create('deposit_requests', function (Blueprint $table) {
            $table->id(); // Identifiant de la demande
            
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('assigned_manager_id')->nullable()->constrained('users')->onDelete('set null'); // nullable car pas forcément assigné dès le départ
            
            $table->string('title'); 
            $table->text('description')->nullable(); 
            $table->string('proposed_file')->nullable(); 
            
            // État actuel de la demande dans le workflow
            $table->enum('status', [
                'pending', 
                'approved_by_manager', 
                'rejected_by_manager', 
                'second_review', 
                'approved', 
                'rejected', 
                'published'
            ])->default('pending');
            
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_requests');
    }
};
