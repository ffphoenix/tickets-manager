<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable('now');

        // Tickets for event1 (organiser1)
        $t1 = new Ticket(
            't0000000-0000-4000-8000-000000000001',
            'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa', // event1
            '11111111-1111-4111-8111-111111111111', // organiser1
            2500,
            'created',
            $now,
        );
        $manager->persist($t1);

        $t2 = new Ticket(
            't0000000-0000-4000-8000-000000000002',
            'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa',
            '11111111-1111-4111-8111-111111111111',
            3500,
            'reserved',
            $now,
        );
        $manager->persist($t2);

        // Tickets for event2 (organiser2)
        $t3 = new Ticket(
            't0000000-0000-4000-8000-000000000003',
            'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb',
            '22222222-2222-4222-8222-222222222222',
            5000,
            'sold',
            $now,
        );
        $manager->persist($t3);

        $t4 = new Ticket(
            't0000000-0000-4000-8000-000000000004',
            'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb',
            '22222222-2222-4222-8222-222222222222',
            5000,
            'cancelled',
            $now,
        );
        $manager->persist($t4);

        // Tickets for event3 (organiser3)
        $t5 = new Ticket(
            't0000000-0000-4000-8000-000000000005',
            'cccccccc-cccc-4ccc-8ccc-cccccccccccc',
            '33333333-3333-4333-8333-333333333333',
            1500,
            'created',
            $now,
        );
        $manager->persist($t5);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class,
            OrganiserFixtures::class,
        ];
    }
}
