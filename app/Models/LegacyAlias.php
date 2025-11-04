<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAlias extends Model
{
    use HasFactory, HasUuid;

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
