<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            
            // Relations
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->boolean('is_internal')->default(false); // Note interne pour les admins
            $table->json('attachments')->nullable(); // Chemins des fichiers joints
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_ticket_replies');
    }
};