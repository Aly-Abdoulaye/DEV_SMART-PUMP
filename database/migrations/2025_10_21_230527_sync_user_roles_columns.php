<?php
// database/migrations/xxxx_xx_xx_xxxxxx_sync_user_roles_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Mapper les valeurs de l'enum 'role' vers les IDs de 'role_id'
        $roleMappings = [
            'super_admin' => 1,
            'admin' => 2,        // Correspond à company-admin
            'manager' => 3,      // Correspond à station-manager
            'employee' => 4,
            'technician' => 5,
        ];

        foreach ($roleMappings as $enumValue => $roleId) {
            DB::table('users')
                ->where('role', $enumValue)
                ->update(['role_id' => $roleId]);
        }

        // Optionnel: Supprimer l'ancienne colonne 'role' si vous voulez
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('role');
        // });
    }

    public function down()
    {
        // Mapper en sens inverse pour rollback
        $reverseMappings = [
            1 => 'super_admin',
            2 => 'admin',
            3 => 'manager',
            4 => 'employee',
            5 => 'technician',
        ];

        foreach ($reverseMappings as $roleId => $enumValue) {
            DB::table('users')
                ->where('role_id', $roleId)
                ->update(['role' => $enumValue]);
        }
    }
};
