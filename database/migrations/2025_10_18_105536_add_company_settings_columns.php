<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Couleurs de l'interface
            $table->string('primary_color')->default('#4e73df')->after('logo');
            $table->string('secondary_color')->default('#858796')->after('primary_color');
            
            // Seuils d'alerte
            $table->decimal('alert_threshold', 8, 2)->default(100.00)->after('secondary_color');
            $table->decimal('low_stock_alert', 8, 2)->default(50.00)->after('alert_threshold');
            $table->integer('maintenance_alert_days')->default(7)->after('low_stock_alert');
            
            // Règles métier
            $table->text('business_rules')->nullable()->after('maintenance_alert_days');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'alert_threshold', 
                'low_stock_alert',
                'maintenance_alert_days',
                'business_rules'
            ]);
        });
    }
};