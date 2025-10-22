<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->string('fuel_type'); // diesel, essence, etc.
            $table->decimal('capacity', 10, 2); // capacité totale
            $table->decimal('current_volume', 10, 2); // volume actuel
            $table->decimal('min_threshold', 10, 2); // seuil minimum
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tanks');
    }
};
