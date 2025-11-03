<?php

namespace App\Organisers\Application\DeleteOrganiser;

final class DeleteOrganiser
{
    public function __construct(
        public readonly string $organiserId,
    ) {}
}
