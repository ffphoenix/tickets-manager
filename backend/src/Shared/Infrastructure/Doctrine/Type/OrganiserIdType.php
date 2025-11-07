<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Organisers\Domain\ValueObject\OrganiserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class OrganiserIdType extends Type
{
    public const NAME = 'organiser_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = $column['length'] ?? 36;
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?OrganiserId
    {
        if ($value === null || $value instanceof OrganiserId) {
            return $value;
        }
        return OrganiserId::fromString((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof OrganiserId) {
            return (string) $value;
        }
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
