<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
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
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'job_title',
        'country',
        'city',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the enrollments for the participant.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the sessions the participant is enrolled in.
     */
    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'enrollments')
            ->withTimestamps();
    }

    /**
     * Get the participant's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
