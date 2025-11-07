<?php

namespace App\Organisers\Domain;

use App\Organisers\Domain\Entity\Organiser;
use App\Organisers\Domain\ValueObject\OrganiserId;
use App\Shared\Domain\Exception\NotFound;

interface OrganiserRepository
{
    public function save(Organiser $organiser): void;

    /**
     * @throws NotFound
     */
    public function get(OrganiserId $id): Organiser;

    public function exists(OrganiserId $id): bool;

    /**
     * Delete organiser by id
     *
     * @throws NotFound
     */
    public function delete(OrganiserId $id): void;

    /**
     * @return Organiser[]
     */
    public function all(): array;
}
