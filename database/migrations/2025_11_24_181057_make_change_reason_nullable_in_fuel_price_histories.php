<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
       public function up()
    {
        Schema::table('fuel_price_histories', function (Blueprint $table) {
            $table->text('change_reason')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('fuel_price_histories', function (Blueprint $table) {
            $table->text('change_reason')->nullable(false)->change();
        });
    }
};
