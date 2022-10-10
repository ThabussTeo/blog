<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i=0;$i<13;$i++) {
            $user = new User();
            $user->setLastname($faker->lastName())
                 ->setFirstname($faker->firstName())
                 ->setUsername($user->getFirstname().$user->getLastname().$i);

            $this->addReference("user".$i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
