<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'training_sessions';

    protected $fillable = [
        'uuid',
        'training_id',
        'delivery_type',
        'start_date',
        'end_date',
        'duration',
        'location',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the training that owns the session.
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * Get the enrollments for the session.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'training_session_id');
    }

    /**
     * Get the participants enrolled in this session.
     */
    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'enrollments', 'training_session_id', 'participant_id')
            ->withTimestamps();
    }
}
