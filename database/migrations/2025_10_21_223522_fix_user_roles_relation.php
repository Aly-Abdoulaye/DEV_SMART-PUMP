<?php
// database/migrations/xxxx_xx_xx_xxxxxx_fix_user_roles_relation.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        // Si role_id est une string, on crée une colonne temporaire
        if (Schema::hasColumn('users', 'role_id') && Schema::getColumnType('users', 'role_id') === 'string') {

            // Ajouter une colonne temporaire pour stocker l'ID numérique
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id_numeric')->nullable()->after('role_id');
            });

            // Mapper les slugs de rôle vers les IDs
            $roleMapping = [
                'super-admin' => 1,
                'company-admin' => 2,
                'station-manager' => 3,
                'employee' => 4,
                'technician' => 5
            ];

            // Mettre à jour les utilisateurs
            foreach ($roleMapping as $slug => $id) {
                User::where('role_id', $slug)->update(['role_id_numeric' => $id]);
            }

            // Supprimer l'ancienne colonne et renommer la nouvelle
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_id');
                $table->renameColumn('role_id_numeric', 'role_id');
            });

            // Ajouter la clé étrangère
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        // En cas de rollback, recréer la colonne string
        if (Schema::hasColumn('users', 'role_id') && Schema::getColumnType('users', 'role_id') === 'bigint') {

            Schema::table('users', function (Blueprint $table) {
                $table->string('role_id_string')->nullable()->after('role_id');
            });

            $roleMapping = [
                1 => 'super-admin',
                2 => 'company-admin',
                3 => 'station-manager',
                4 => 'employee',
                5 => 'technician'
            ];

            foreach ($roleMapping as $id => $slug) {
                User::where('role_id', $id)->update(['role_id_string' => $slug]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
                $table->renameColumn('role_id_string', 'role_id');
            });
        }
    }
};
