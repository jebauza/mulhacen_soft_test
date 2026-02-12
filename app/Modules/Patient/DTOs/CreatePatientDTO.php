<?php

namespace App\Modules\Patient\DTOs;

class CreatePatientDTO
{
    const NAME = 'name';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const NOTES = 'notes';

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $notes
    ) {}

    public function toArray(bool $onlyUser = false): array
    {
        $data = [
            self::NAME          => $this->name,
            self::EMAIL         => $this->email,
            self::PHONE         => $this->phone,
            self::NOTES         => $this->notes,
        ];

        return array_filter($data, fn($value) => !is_null($value));
    }

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->{self::NAME},
            email: $request->{self::EMAIL},
            phone: $request->{self::PHONE},
            notes: $request->{self::NOTES}
        );
    }
}
