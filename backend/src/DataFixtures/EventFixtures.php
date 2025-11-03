<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public const EVENT_1_REF = 'event_launch';
    public const EVENT_2_REF = 'event_summit';
    public const EVENT_3_REF = 'event_meetup';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable('now');

        // Use organiser IDs from OrganiserFixtures (string IDs in entity model)
        $event1 = new Event(
            'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa',
            'Product Launch',
            '11111111-1111-4111-8111-111111111111',
            new \DateTimeImmutable('+7 days 10:00'),
            new \DateTimeImmutable('+7 days 12:00'),
            $now,
        );
        $manager->persist($event1);
        $this->addReference(self::EVENT_1_REF, $event1);

        $event2 = new Event(
            'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb',
            'Tech Summit',
            '22222222-2222-4222-8222-222222222222',
            new \DateTimeImmutable('+14 days 09:00'),
            new \DateTimeImmutable('+14 days 17:00'),
            $now,
        );
        $manager->persist($event2);
        $this->addReference(self::EVENT_2_REF, $event2);

        $event3 = new Event(
            'cccccccc-cccc-4ccc-8ccc-cccccccccccc',
            'Community Meetup',
            '33333333-3333-4333-8333-333333333333',
            new \DateTimeImmutable('+21 days 18:00'),
            new \DateTimeImmutable('+21 days 20:00'),
            $now,
        );
        $manager->persist($event3);
        $this->addReference(self::EVENT_3_REF, $event3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrganiserFixtures::class,
        ];
    }
}
