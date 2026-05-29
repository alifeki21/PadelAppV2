<?php

namespace App\DataFixtures;

use App\Entity\Court;
use App\Entity\Tournament;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On crée les terrains (Courts)
        $court1 = new Court();
        $court1->setName('Court 1 - Cupra');
        $court1->setIsIndoor(0); // Outdoor
        $court1->setPricePerHour(25.00); 
        $court1->setImage('cupra.png');
        $manager->persist($court1);

        $court2 = new Court();
        $court2->setName('Court 2 - Decathlon');
        $court2->setIsIndoor(1); // Indoor
        $court2->setPricePerHour(25.00);
        $court2->setImage('dechatlon.png');
        $manager->persist($court2);

        $court3 = new Court();
        $court3->setName('Court 3 - Codeforces');
        $court3->setIsIndoor(0);
        $court3->setPricePerHour(15.00);
        $court3->setImage('codeforces.png');
        $manager->persist($court3);

        // On crée les tournois (Tournaments)
        $tournamentsData = [
            [
                'name' => 'Open de Printemps',
                'startDate' => '2026-06-15',
                'endDate' => '2026-06-17',
                'level' => 'P100',
                'maxTeams' => 16,
                'status' => 'upcoming',
            ],
            [
                'name' => 'Tournoi des Champions',
                'startDate' => '2026-07-10',
                'endDate' => '2026-07-12',
                'level' => 'P250',
                'maxTeams' => 24,
                'status' => 'upcoming',
            ],
            [
                'name' => 'Coupe d\'Été Amateurs',
                'startDate' => '2026-08-05',
                'endDate' => '2026-08-06',
                'level' => 'P25',
                'maxTeams' => 12,
                'status' => 'upcoming',
            ],
            [
                'name' => 'Masters Padel Tour',
                'startDate' => '2026-09-20',
                'endDate' => '2026-09-23',
                'level' => 'P1000',
                'maxTeams' => 32,
                'status' => 'upcoming',
            ],
            [
                'name' => 'Tournoi de Noël',
                'startDate' => '2025-12-18',
                'endDate' => '2025-12-20',
                'level' => 'P100',
                'maxTeams' => 16,
                'status' => 'completed',
            ],
            [
                'name' => 'Challenge Débutants',
                'startDate' => '2026-05-10',
                'endDate' => '2026-05-11',
                'level' => 'P25',
                'maxTeams' => 8,
                'status' => 'ongoing',
            ],
        ];

        foreach ($tournamentsData as $data) {
            $tournament = new Tournament();
            $tournament->setName($data['name']);
            $tournament->setStartDate(new \DateTime($data['startDate']));
            $tournament->setEndDate(new \DateTime($data['endDate']));
            $tournament->setLevel($data['level']);
            $tournament->setMaxTeams($data['maxTeams']);
            $tournament->setStatus($data['status']);
            $manager->persist($tournament);
        }

        $manager->flush();
    }
}