<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // Ex: user.login, company.create, sale.update
            $table->text('description');
            $table->json('old_values')->nullable(); // Valeurs avant modification
            $table->json('new_values')->nullable(); // Valeurs après modification
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // HTTP method: GET, POST, etc.
            
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            
            // Détection d'activités suspectes
            $table->boolean('suspicious')->default(false);
            $table->string('risk_level')->nullable(); // low, medium, high
            $table->text('suspicious_reason')->nullable();
            
            $table->timestamps();
        });

        // Index pour les performances
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['company_id', 'created_at']);
            $table->index(['suspicious', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};