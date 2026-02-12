<?php

namespace App\Modules\Patient\Repositories;

use App\Common\Repositories\BaseRepository;
use App\Modules\Patient\Models\Patient;

class PatientRepository extends BaseRepository
{
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }
}
