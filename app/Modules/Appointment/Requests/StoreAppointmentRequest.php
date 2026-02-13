<?php

namespace App\Modules\Appointment\Requests;

use App\Common\Requests\ApiRequest;
use App\Modules\Appointment\DTOs\CreateAppointmentDTO;
use App\Modules\Patient\Models\Patient;
use App\Modules\Treatment\Models\Treatment;
use App\Modules\Treatment\Repositories\TreatmentRepository;
use App\Modules\User\Models\Dentist;

class StoreAppointmentRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            CreateAppointmentDTO::PATIENT_ID => 'required|UUID|exists:' . Patient::TABLE . ',' . Patient::ID,
            CreateAppointmentDTO::DENTIST_ID => 'required|UUID|exists:' . Dentist::TABLE . ',' . Dentist::ID,
            CreateAppointmentDTO::STAR => 'required|date_format:Y-m-d H:i:s',
            CreateAppointmentDTO::END => 'required|date_format:Y-m-d H:i:s',
            CreateAppointmentDTO::DURATION => 'required|integer|min:1|max:65535',
            CreateAppointmentDTO::REASON => 'nullable|string',

            CreateAppointmentDTO::TREATMENT_IDS => 'present|array|min:1',
            CreateAppointmentDTO::TREATMENT_IDS . '.*' => 'uuid',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($validator->errors()->all())) {
                $this->checkTreatment($validator);
            }
        });
    }

    public function checkTreatment($validator): void
    {
        $treatmentRepo = app(TreatmentRepository::class);
        $validIds = $treatmentRepo->whereIn(Treatment::ID, $this->{CreateAppointmentDTO::TREATMENT_IDS})->pluck(Treatment::ID);
        $notValidIds = collect($this->{CreateAppointmentDTO::TREATMENT_IDS})->diff($validIds);

        foreach ($notValidIds as $key => $id) {
            $validator->errors()->add(
                CreateAppointmentDTO::TREATMENT_IDS . ".$key",
                "The treatment ($id) is not valid."
            );
        }
    }
}
