<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->string('name')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
