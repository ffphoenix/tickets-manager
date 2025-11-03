<?php

namespace App\Organisers\Application\CreateOrganiser;

final class CreateOrganiser
{
    public function __construct(
        public readonly string $organiserId,
        public readonly string $name,
    ) {}
}
