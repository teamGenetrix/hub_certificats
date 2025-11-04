<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'code',
        'title',
        'has_exam',
        'is_custom',
        'custom_code',
        'description',
        'pillar_id',
    ];

    protected $casts = [
        'has_exam' => 'boolean',
        'is_custom' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the pillar that owns the training.
     */
    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    /**
     * Get the sessions for the training.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
