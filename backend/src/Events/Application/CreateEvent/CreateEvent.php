<?php

namespace App\Events\Application\CreateEvent;

final class CreateEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $organiserId,
        public readonly string $name,
        public readonly \DateTimeImmutable $startAt,
        public readonly \DateTimeImmutable $endAt,
    ) {}
}
