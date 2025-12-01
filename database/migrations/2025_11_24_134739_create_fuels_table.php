<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Essence Super, Gazole, etc.
            $table->string('code')->unique(); // ESS95, GAZOLE, etc.
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Prix actuel
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuels');
    }
};