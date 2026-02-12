<?php

namespace App\Modules\Specialty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Specialty extends Model
{
    use HasUuids;

    const TABLE = 'specialties';

    protected $table = self::TABLE;
    protected $primaryKey = self::ID;
    public $incrementing = false;
    protected $keyType = 'string';

    const ID = 'id';
    const NAME = 'name';

    protected $fillable = [
        self::NAME,
    ];
}
