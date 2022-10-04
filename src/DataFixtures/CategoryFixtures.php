<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;


class CategoryFixtures extends Fixture {

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void {

        $faker = Factory::create();

        for ($i=0;$i<11;$i++) {
            $category = new Category();
            $category->setTitle($faker->word())
                     ->setSlug($this->slugger->slug($category->getTitle())->lower());

            // Créer une référence sur la catégorie
            $this->addReference("categories".$i, $category);

            $manager->persist($category);

        }

        $manager->flush();
    }
}
