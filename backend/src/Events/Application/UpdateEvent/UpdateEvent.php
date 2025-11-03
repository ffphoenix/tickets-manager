<?php

namespace App\Events\Application\UpdateEvent;

final class UpdateEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $name,
        public readonly \DateTimeImmutable $startAt,
        public readonly \DateTimeImmutable $endAt,
    ) {}
}
