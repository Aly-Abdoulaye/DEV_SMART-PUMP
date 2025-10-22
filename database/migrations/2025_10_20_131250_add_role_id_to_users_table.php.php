<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_role_id_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            // $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            // $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();

            $table->index('role_id');
            // $table->index('company_id');
            // $table->index('is_active');
            $table->index('last_login_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            // $table->dropForeign(['company_id']);
            $table->dropColumn(['role_id', 'last_login_at', 'notes', 'settings']);
        });
    }
};
