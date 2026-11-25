<?php

namespace App\Models;

use App\Enums\ReferenceType;
use Database\Factories\CvReferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'full_name',
    'type',
    'company',
    'position',
    'phone',
    'email',
])]
class CvReference extends Model
{
    /** @use HasFactory<CvReferenceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ReferenceType::class,
        ];
    }

    /**
     * Get the user that owns the reference.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
