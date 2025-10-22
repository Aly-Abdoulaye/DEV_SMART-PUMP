<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // maintenance, update, alert, info, billing
            $table->string('subject');
            $table->text('message');
            $table->json('metadata')->nullable(); // Données supplémentaires
            
            // Relations
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            
            // Statut de livraison
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Planning pour les notifications futures
            $table->timestamp('scheduled_for')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            
            $table->timestamps();
        });

        // Index pour les performances
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['company_id', 'is_sent']);
            $table->index(['type', 'created_at']);
            $table->index(['scheduled_for', 'is_sent']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};