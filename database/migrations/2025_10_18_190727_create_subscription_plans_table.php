<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // basic, premium, enterprise
            $table->string('display_name'); // Basic, Premium, Enterprise
            $table->text('description')->nullable();
            
            // Tarification
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('annual_price', 10, 2)->default(0);
            $table->decimal('setup_fee', 10, 2)->default(0);
            
            // Limites
            $table->integer('max_stations')->default(1);
            $table->integer('max_users')->default(5);
            $table->integer('max_customers')->default(100);
            $table->boolean('has_advanced_reports')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_premium_support')->default(false);
            
            // Statut
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });

        // Données par défaut
        $this->seedDefaultPlans();
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }

    private function seedDefaultPlans()
    {
        // Cette méthode sera appelée après la création de la table
    }
};