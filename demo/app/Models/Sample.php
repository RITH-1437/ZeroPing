<?php

namespace App\Models;

use App\Core\Database\Model;

/**
 * Sample — an illustrative model for a generated ZeroPing application.
 *
 * Models extend App\Core\Database\Model and map to a database table via the
 * `$table` property. Common conventions:
 *  - `$fillable` lists columns safe for mass assignment.
 *  - `$casts` casts columns to native types on read/write.
 */
class Sample extends Model
{
    /** @var string The database table for this model. */
    protected string $table = 'samples';

    /** @var array<int,string> Columns that may be mass-assigned. */
    protected array $fillable = ['title', 'body', 'published'];

    /** @var array<string,string> Attribute cast types. */
    protected array $casts = [
        'published' => 'bool',
        'created_at' => 'datetime',
    ];
}
