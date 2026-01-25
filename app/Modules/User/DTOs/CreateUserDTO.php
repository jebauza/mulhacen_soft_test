<?php

namespace App\Modules\User\DTOs;

class CreateUserDTO
{
    const EMAIL = 'email';
    const NAME = 'name';
    const PASSWORD = 'password';

    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public string $password,
    ) {}

    public function toArray(bool $onlyUser = false): array
    {
        $data = [
            self::EMAIL         => $this->email,
            self::NAME          => $this->name,
            self::PASSWORD      => $this->password,
        ];

        return array_filter($data, fn($value) => !is_null($value));
    }

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->{self::EMAIL},
            name: $request->{self::NAME},
            password: $request->{self::PASSWORD},
        );
    }
}
