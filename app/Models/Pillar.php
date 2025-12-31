<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pillar extends Model
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
        'name',
        'code',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the trainings for the pillar.
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }
}
