<?php

namespace App\Modules\Treatment\Repositories;

use App\Common\Repositories\BaseRepository;
use App\Modules\Treatment\Models\Treatment;

class TreatmentRepository extends BaseRepository
{
    public function __construct(Treatment $model)
    {
        parent::__construct($model);
    }
}
