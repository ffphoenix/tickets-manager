<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Events\Domain\ValueObject\EventId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class EventIdType extends Type
{
    public const NAME = 'event_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = $column['length'] ?? 36;
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EventId
    {
        if ($value === null || $value instanceof EventId) {
            return $value;
        }
        return EventId::fromString((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof EventId) {
            return (string) $value;
        }
        // Allow raw string for safety in DQL parameters
        return (string) $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
