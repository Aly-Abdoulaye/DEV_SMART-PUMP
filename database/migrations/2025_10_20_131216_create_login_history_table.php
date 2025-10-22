<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_login_history_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at');
            $table->boolean('success')->default(true);
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'login_at']);
            $table->index('ip_address');
            $table->index('success');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_history');
    }
};
