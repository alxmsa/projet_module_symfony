<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'admin-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@blog.test');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin1234')
        );
        $manager->persist($admin);

        $this->addReference(self::ADMIN_REFERENCE, $admin);

        // Users
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setRoles(['ROLE_USER']);
            $user->setIsActive(true);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'user1234')
            );

            $manager->persist($user);
            $this->addReference('user_'.$i, $user);
        }

        $manager->flush();
    }
}