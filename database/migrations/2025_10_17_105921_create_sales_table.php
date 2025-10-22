<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pump_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // employé
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade'); // client partenaire
            $table->decimal('volume', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('start_index', 15, 3);
            $table->decimal('end_index', 15, 3);
            $table->enum('payment_method', ['cash', 'customer_account', 'card', 'mobile_money']);
            $table->enum('status', ['completed', 'cancelled', 'pending'])->default('completed');
            $table->timestamp('sale_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
