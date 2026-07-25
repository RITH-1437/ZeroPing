<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Blueprint;

/**
 * Create the samples table.
 *
 * The first migration in a ZeroPing app. `php zero migrate` runs every
 * migration's up() in filename order; down() reverses it.
 *
 * @see https://zero-ping.duckdns.org/docs/introduction
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('samples');
    }
};
