<?php

namespace App\DataFixtures;

use App\Entity\Court;
use Doctrine\Bundle\DoctrineBundle\Fixture\Fixture;
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
        $court1->setImage('court1.png');
        $manager->persist($court1);

        $court2 = new Court();
        $court2->setName('Court 2 - Decathlon');
        $court2->setIsIndoor(1); // Indoor
        $court2->setPricePerHour(25.00);
        $court2->setImage('court2.png');
        $manager->persist($court2);

        $court3 = new Court();
        $court3->setName('Court 3 - Codeforces');
        $court3->setIsIndoor(0);
        $court3->setPricePerHour(15.00);
        $court3->setImage('court3.png');
        $manager->persist($court3);

        $manager->flush();
    }
}