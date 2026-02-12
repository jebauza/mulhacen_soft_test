<?php

namespace App\Modules\User\DTOs;

class UpdateUserDTO
{
    const EMAIL = 'email';
    const NAME = 'name';
    const PASSWORD = 'password';

    public function __construct(
        public readonly string $email,
        public string $password,
    ) {}

    public function toArray(bool $onlyUser = false): array
    {
        $data = [
            self::EMAIL         => $this->email,
            self::PASSWORD      => $this->password,
        ];

        return array_filter($data, fn($value) => !is_null($value));
    }

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->{self::EMAIL},
            password: $request->{self::PASSWORD},
        );
    }
}
