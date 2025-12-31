<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAlias extends Model
{
    use HasFactory, HasUuid;

    // Si ton uuid est une string
    protected $keyType = 'string';

    // Si ton uuid n’est pas auto-increment, sinon laisse à false uniquement si tu utilises uuid comme PK
    // public $incrementing = false;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $fillable = [
        'uuid',
        'old_reference',
        'reference_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the reference that owns the legacy alias.
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }
}
