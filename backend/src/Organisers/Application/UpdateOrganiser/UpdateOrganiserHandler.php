<?php

namespace App\Organisers\Application\UpdateOrganiser;

use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;

final class UpdateOrganiserHandler
{
    public function __construct(private readonly OrganiserRepository $organisers)
    {
    }

    public function __invoke(UpdateOrganiser $command): void
    {
        $id = OrganiserId::fromString($command->organiserId);
        $organiser = $this->organisers->get($id);
        $organiser->rename($command->name);
        $this->organisers->save($organiser);
    }
}
