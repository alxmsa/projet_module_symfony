<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['Symfony', 'Articles about Symfony framework.'],
            ['PHP', 'Modern PHP development.'],
            ['Backend', 'Architecture and APIs.'],
        ];

        foreach ($categories as $index => [$name, $desc]) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($desc);

            $manager->persist($category);
            $this->addReference('category_'.$index, $category);
        }

        $manager->flush();
    }
}