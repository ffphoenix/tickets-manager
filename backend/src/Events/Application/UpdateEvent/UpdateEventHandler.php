<?php

namespace App\Events\Application\UpdateEvent;

use App\Events\Domain\EventRepository;
use App\Events\Domain\ValueObject\EventId;

final class UpdateEventHandler
{
    public function __construct(private readonly EventRepository $events)
    {
    }

    public function __invoke(UpdateEvent $command): void
    {
        $id = EventId::fromString($command->eventId);
        $event = $this->events->get($id);
        $event->rename($command->name);
        $event->reschedule($command->startAt, $command->endAt);
        $this->events->save($event);
    }
}
