<?php

namespace App\Organisers\Domain;

use App\Shared\Domain\Exception\NotFound;

interface OrganiserRepository
{
    public function save(Organiser $organiser): void;

    /**
     * @throws NotFound
     */
    public function get(OrganiserId $id): Organiser;

    public function exists(OrganiserId $id): bool;
}
