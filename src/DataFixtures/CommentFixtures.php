<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Validator\Constraints\Date;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [ArticleFixtures::class , UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0;$i<100;$i++) {

                $comment = new Comment();
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setContent($faker->words(5, true));
                $comment->setArticle($this->getReference("article".$faker->numberBetween(0, 100)));

                if ($faker->numberBetween(0,5) < 4) {
                    $comment->setUser($this->getReference("user".$faker->numberBetween(0, 12)));
                }

                $manager->persist($comment);
            }


                $manager->flush();


        }



}
