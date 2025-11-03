<?php

namespace App\Events\Domain;

use App\Organisers\Domain\OrganiserId;

final class Event
{
    public function __construct(
        private readonly EventId $id,
        private string $name,
        private readonly OrganiserId $organiserId,
        private \DateTimeImmutable $startAt,
        private \DateTimeImmutable $endAt,
        private readonly \DateTimeImmutable $createdAt,
    ) {
        $this->assertDates($startAt, $endAt);
    }

    public static function schedule(EventId $id, string $name, OrganiserId $organiserId, \DateTimeImmutable $startAt, \DateTimeImmutable $endAt): self
    {
        self::assertStaticDates($startAt, $endAt);
        $name = trim($name);
        if ($name == '') {
            throw new \InvalidArgumentException('Event name cannot be empty');
        }
        return new self($id, $name, $organiserId, $startAt, $endAt, new \DateTimeImmutable());
    }

    private function assertDates(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): void
    {
        self::assertStaticDates($startAt, $endAt);
    }

    private static function assertStaticDates(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): void
    {
        if ($endAt <= $startAt) {
            throw new \InvalidArgumentException('Event endAt must be after startAt');
        }
    }

    public function id(): EventId { return $this->id; }
    public function name(): string { return $this->name; }
    public function organiserId(): OrganiserId { return $this->organiserId; }
    public function startAt(): \DateTimeImmutable { return $this->startAt; }
    public function endAt(): \DateTimeImmutable { return $this->endAt; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function rename(string $name): void
    {
        $name = trim($name);
        if ($name == '') {
            throw new \InvalidArgumentException('Event name cannot be empty');
        }
        $this->name = $name;
    }

    public function reschedule(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): void
    {
        $this->assertDates($startAt, $endAt);
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }
}
