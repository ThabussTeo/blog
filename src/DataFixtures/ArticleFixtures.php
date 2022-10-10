<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface {

    private SluggerInterface $slugger;

    // Demander à symfony d'injecter le slugger dans le constructeur.

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void {
        $faker = Factory::create();



        for ($i=0;$i<101;$i++) {
            $article = new Article();
            $article->setTitle($faker->words($faker->numberBetween(3, 10), true))
                ->setContent($faker->paragraph(3, true))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setSlug($this->slugger->slug($article->getTitle())->lower())
                ->setCategory($this->getReference("categories".$faker->numberBetween(0, 10)))
                ->setIsPublished($faker->numberBetween(0, 3) > 0);

            $this->addReference("article".$i, $article);

            $manager->persist($article);

        }

        $manager->flush();
    }

    public function getDependencies() {
        return [CategoryFixtures::class];
    }
}
