<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pumps', function (Blueprint $table) {
            $table->id();
            $table->string('pump_number'); // numéro de la pompe
            $table->string('nozzle_number'); // numéro du pistolet
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->decimal('initial_index', 15, 3)->default(0); // index initial
            $table->decimal('current_index', 15, 3)->default(0); // index courant
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pumps');
    }
};
