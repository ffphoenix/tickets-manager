<?php

namespace App\Events\Domain;

use App\Shared\Domain\Exception\NotFound;

interface EventRepository
{
    public function save(Event $event): void;

    /**
     * @throws NotFound
     */
    public function get(EventId $id): Event;

    public function exists(EventId $id): bool;

    /**
     * Deletes the event by id.
     *
     * @throws NotFound
     */
    public function delete(EventId $id): void;

    /**
     * Returns all events ordered by startAt ascending.
     *
     * @return Event[]
     */
    public function all(): array;
}
