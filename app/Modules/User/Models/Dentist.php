<?php

namespace App\Modules\User\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Specialty\Models\Specialty;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dentist extends Model
{
    use HasUuids;

    const TABLE = 'dentists';

    protected $table = self::TABLE;
    protected $primaryKey = self::ID;
    public $incrementing = false;
    protected $keyType = 'string';

    const ID = 'id';
    const USER_ID = 'user_id';
    const NAME = 'name';
    const LAST_NAME = 'last_name';

    protected $fillable = [self::USER_ID, self::NAME, self::LAST_NAME];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID);
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(
            Specialty::class,
            'dentist_specialty',
            'dentist_id',
            'specialty_id'
        )
            ->withPivot('created_at', 'updated_at')
            ->withTimestamps();
    }
}
