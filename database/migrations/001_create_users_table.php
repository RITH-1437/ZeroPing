<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('username', 100);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->enum('role', ['admin', 'owner', 'customer'])->default('customer');
            $table->string('phone', 100)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};
