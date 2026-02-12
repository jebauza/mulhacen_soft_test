<?php

namespace App\Modules\Treatment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Treatment extends Model
{
    use HasUuids;

    const TABLE = 'treatments';

    protected $table = self::TABLE;
    protected $primaryKey = self::ID;
    public $incrementing = false;
    protected $keyType = 'string';

    const ID = 'id';
    const SPECIALTY_ID = 'specialty_id';
    const NAME = 'name';
    const BASE_AMOUNT = 'base_amount';
    const DURATION = 'duration';

    protected $fillable = [
        self::SPECIALTY_ID,
        self::NAME,
        self::BASE_AMOUNT,
        self::DURATION
    ];
}
