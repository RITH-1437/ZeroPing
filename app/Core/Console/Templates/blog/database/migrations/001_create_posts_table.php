<?php

use App\Core\Database\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->schema->create('posts', function ($table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('excerpt')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->drop('posts');
    }
};
