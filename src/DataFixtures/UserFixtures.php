<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


        public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("moh@gmail.com");
        $password = $this->hasher->hashPassword($user, 'moh1234');
        $user->setPassword($password);
        $user->setRoles(array("ROLE_ADMIN", "ROLE_USER"));
        $manager->persist($user);


        $user1 = new User();
        $user1->setEmail("mike@gmail.com");
        $password = $this->hasher->hashPassword($user, 'mike1234');
        $user1->setPassword($password);
        $user1->setRoles(array("ROLE_USER"));
        $manager->persist($user1);


        $manager->flush();
    }
}
