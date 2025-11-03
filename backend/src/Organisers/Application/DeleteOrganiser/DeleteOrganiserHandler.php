<?php

namespace App\Organisers\Application\DeleteOrganiser;

use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;

final class DeleteOrganiserHandler
{
    public function __construct(private readonly OrganiserRepository $organisers)
    {
    }

    public function __invoke(DeleteOrganiser $command): void
    {
        $id = OrganiserId::fromString($command->organiserId);
        $this->organisers->delete($id);
    }
}
