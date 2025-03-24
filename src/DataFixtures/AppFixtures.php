<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Throwable\LoadingThrowable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws LoadingThrowable
     */
    public function load(ObjectManager $manager): void
    {
        $loader = new NativeLoader();

        $objectSet = $loader->loadFile(__DIR__ . '/Fixtures/user_admin.yml');
        foreach ($objectSet->getObjects() as $object) {
            if ($object instanceof User) {
                $hashedPassword = $this->passwordHasher->hashPassword($object, $object->getPassword());
                $object->setPassword($hashedPassword);
            }

            $manager->persist($object);
        }

        $manager->flush();
    }
}
