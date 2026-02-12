<?php

namespace App\Modules\Appointment\Models;

use App\Modules\User\Models\Dentist;
use App\Modules\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Treatment\Models\Treatment;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    use HasUuids;

    const TABLE = 'appointments';

    protected $table = self::TABLE;
    protected $primaryKey = self::ID;
    public $incrementing = false;
    protected $keyType = 'string';

    const ID = 'id';
    const PATIENT_ID = 'patient_id';
    const DENTIST_ID = 'dentist_id';
    const START = 'start';
    const END = 'end';
    const DURATION = 'duration';
    const REASON = 'reason';

    protected $fillable = [
        self::PATIENT_ID,
        self::DENTIST_ID,
        self::START,
        self::END,
        self::DURATION,
        self::REASON,
    ];

    protected function casts(): array
    {
        return [
            self::START => 'datetime',
            self::END => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, self::PATIENT_ID, Patient::ID);
    }

    public function dentist(): BelongsTo
    {
        return $this->belongsTo(Dentist::class, self::DENTIST_ID, Dentist::ID);
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(
            Treatment::class,
            'appointment_treatment',
            'appointment_id',
            'treatment_id'
        )
            ->withPivot('created_at', 'updated_at')
            ->withTimestamps();
    }
}
