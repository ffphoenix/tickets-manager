<?php

namespace App\Organisers\Application\CreateOrganiser;

use App\Organisers\Domain\Organiser;
use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;

final class CreateOrganiserHandler
{
    public function __construct(private readonly OrganiserRepository $organisers)
    {
    }

    public function __invoke(CreateOrganiser $command): void
    {
        $organiser = Organiser::create(
            OrganiserId::fromString($command->organiserId),
            $command->name,
        );

        $this->organisers->save($organiser);
    }
}
