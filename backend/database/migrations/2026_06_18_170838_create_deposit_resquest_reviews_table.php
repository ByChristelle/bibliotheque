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
       Schema::create('deposit_request_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('reviewer_role', ['responsable_demande', 'admin']);
            $table->enum('decision', ['approved', 'rejected', 'override', 'second_opinion_requested']);
            $table->text('justification')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_resquest_reviews');
    }
};
