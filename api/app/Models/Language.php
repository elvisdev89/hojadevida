<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

/**
 * Catalog of ISO 639-1 languages, loaded by a seeder.
 */
#[WithoutTimestamps]
#[Fillable(['code', 'name'])]
class Language extends Model
{
    //
}
