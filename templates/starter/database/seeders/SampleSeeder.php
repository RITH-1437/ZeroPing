<?php

namespace App\Database\Seeders;

use App\Models\Sample;

/**
 * SampleSeeder populates the generated samples table with one visible record.
 * Run it with `php zero db:seed` after running migrations.
 */
class SampleSeeder
{
    public function run(): void
    {
        Sample::create([
            'title' => 'Hello, ZeroPing',
            'body' => 'This sample record was created by SampleSeeder.',
            'published' => true,
        ]);
    }
}
