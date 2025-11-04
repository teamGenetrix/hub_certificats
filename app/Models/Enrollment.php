<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'participant_id',
        'training_session_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the participant that owns the enrollment.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the session that owns the enrollment.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'training_session_id');
    }

    /**
     * Get the reference associated with the enrollment.
     */
    public function reference(): HasOne
    {
        return $this->hasOne(Reference::class);
    }
}
