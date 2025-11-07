<?php

namespace App\Events\Application\CreateEvent;

use App\Events\Domain\Entity\Event;
use App\Events\Domain\EventRepository;
use App\Events\Domain\ValueObject\EventId;
use App\Organisers\Domain\ValueObject\OrganiserId;

final class CreateEventHandler
{
    public function __construct(private readonly EventRepository $events)
    {
    }

    public function __invoke(CreateEvent $command): void
    {
        $event = Event::schedule(
            EventId::fromString($command->eventId),
            $command->name,
            OrganiserId::fromString($command->organiserId),
            $command->startAt,
            $command->endAt,
        );

        $this->events->save($event);
    }
}
