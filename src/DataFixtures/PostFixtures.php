<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;

final class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');

        $admin = $this->getReference(UserFixtures::ADMIN_REFERENCE, User::class);

        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence());
            $post->setContent($faker->paragraphs(5, true));
            $post->setPicture('https://picsum.photos/seed/post'.$i.'/900/400');
            $post->setPublishedAt(new \DateTimeImmutable());
            $post->setAuthor($admin);
            $post->setCategory(
                $this->getReference('category_'.rand(0, 2), Category::class)
            );

            $manager->persist($post);
            $this->addReference('post_'.$i, $post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}