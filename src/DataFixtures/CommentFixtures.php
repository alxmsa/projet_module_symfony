<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Post;

final class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');

        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->sentence(15));
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor(
                $this->getReference('user_'.rand(0, 4), User::class)
            );
            $comment->setPost(
                $this->getReference('post_'.rand(0, 9), Post::class)
            );

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixtures::class,
            UserFixtures::class,
        ];
    }
}