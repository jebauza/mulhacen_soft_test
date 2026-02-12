<?php

namespace App\Modules\Patient\Services;

use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use App\Modules\Patient\Repositories\PatientRepository;

class PatientService
{
    public function __construct(
        protected readonly PatientRepository $patientRepo
    ) {}

    public function create(CreatePatientDTO $dto): Patient
    {
        return $this->patientRepo->create($dto->toArray());
    }
}
