<?php

namespace App\Modules\Appointment\DTOs;


class CreateAppointmentDTO
{
    const PATIENT_ID = 'patient_id';
    const DENTIST_ID = 'dentist_id';
    const STAR = 'start';
    const END = 'end';
    const DURATION = 'duration';
    const REASON = 'reason';
    const TREATMENT_IDS = 'treatment_ids';

    public function __construct(
        public readonly string $patient_id,
        public readonly string $dentist_id,
        public readonly string $start,
        public readonly string $end,
        public readonly int $duration,
        public readonly ?string $reason = null,
        public readonly array $treatment_ids = []
    ) {}

    public function toArray(bool $onlyModel = false): array
    {
        $data = [
            self::PATIENT_ID          => $this->patient_id,
            self::DENTIST_ID         => $this->dentist_id,
            self::STAR         => $this->start,
            self::END         => $this->end,
            self::DURATION         => $this->duration,
            self::REASON         => $this->reason,
        ];

        if (!$onlyModel) {
            $data[self::TREATMENT_IDS] = $this->{self::TREATMENT_IDS};
        }

        return array_filter($data, fn($value) => !is_null($value));
    }

    public static function fromRequest($request): self
    {
        return new self(
            patient_id: $request->{self::PATIENT_ID},
            dentist_id: $request->{self::DENTIST_ID},
            start: $request->{self::STAR},
            end: $request->{self::END},
            duration: $request->{self::DURATION},
            reason: $request->{self::REASON},
            treatment_ids: $request->{self::TREATMENT_IDS},
        );
    }
}
