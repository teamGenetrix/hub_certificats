<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reference extends Model
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
        'doc_kind',
        'reference',
        'month_year',
        'global_increment',
        'pillar_increment',
        'pillar_id',
        'increment_no',
        'meta',
        'enrollment_id',
    ];

    protected $casts = [
        'increment_no' => 'integer',
        'global_increment' => 'integer',
        'pillar_increment' => 'integer',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the enrollment that owns the reference.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the pillar that owns the reference.
     */
    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    /**
     * Get the legacy aliases for the reference.
     */
    public function legacyAliases(): HasMany
    {
        return $this->hasMany(LegacyAlias::class);
    }
}
