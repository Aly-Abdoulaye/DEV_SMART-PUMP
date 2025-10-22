<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // NUMéro unique du paiement
            $table->decimal('amount', 10, 2); // Montant payé
            $table->string('currency')->default('XOF'); // Devise
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'mobile_money', 'card', 'cash'])->default('bank_transfer');
            $table->string('transaction_id')->nullable(); // ID de transaction externe
            
            // Période couverte par le paiement
            $table->date('start_date'); // Début de la période
            $table->date('end_date');   // Fin de la période
            
            // Relations
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
            
            // Données de facturation
            $table->string('invoice_number')->unique()->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Index pour les performances
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['payment_number']);
            $table->index(['invoice_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};