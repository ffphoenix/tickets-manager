<?php

namespace App\DataFixtures;

use App\Entity\Organiser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrganiserFixtures extends Fixture
{
    public const ORG_1_REF = 'organiser_acme';
    public const ORG_2_REF = 'organiser_globex';
    public const ORG_3_REF = 'organiser_initech';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable('now');

        $org1 = new Organiser(
            '11111111-1111-4111-8111-111111111111',
            'Acme Corp',
            $now,
        );
        $manager->persist($org1);
        $this->addReference(self::ORG_1_REF, $org1);

        $org2 = new Organiser(
            '22222222-2222-4222-8222-222222222222',
            'Globex',
            $now,
        );
        $manager->persist($org2);
        $this->addReference(self::ORG_2_REF, $org2);

        $org3 = new Organiser(
            '33333333-3333-4333-8333-333333333333',
            'Initech',
            $now,
        );
        $manager->persist($org3);
        $this->addReference(self::ORG_3_REF, $org3);

        $manager->flush();
    }
}
