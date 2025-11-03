<?php

namespace App\Organisers\Application\UpdateOrganiser;

final class UpdateOrganiser
{
    public function __construct(
        public readonly string $organiserId,
        public readonly string $name,
    ) {}
}
