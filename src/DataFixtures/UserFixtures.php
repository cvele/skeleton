<?php

namespace App\DataFixtures;

use App\Service\Security\UserManipulator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class UserFixtures extends Fixture
{
    /** @var UserManipulator **/
    private $userManipulator;

    /** @var FakerGenerator **/
    private $fakerFactory;

    /**
     * @param UserManipulator $userManipulator
     */
    public function __construct(UserManipulator $userManipulator)
    {
        /** @var UserManipulator **/
        $this->userManipulator = $userManipulator;
        /** @var FakerGenerator **/
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 20; $i++) {
            $safeEmail = $this->faker->unique()->safeEmail;
            $user = $this->userManipulator->createUserObject();
            $user->setEmail($safeEmail);
            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            $user->setPlainPassword($this->faker->password);
            $user->setActive($this->faker->boolean($chanceOfGettingTrue = 90));
            $user->setConfirmed($this->faker->boolean($chanceOfGettingTrue = 80));
            $user->setTenant($this->getReference("tenant-" . rand(1,2)));
            $this->userManipulator->create($user);
            $this->addReference("user-reference-" . $safeEmail, $user);
        }
    }

    /** @inheritDoc **/
    public function getDependencies()
    {
        return [
            TenantFixtures::class,
        ];
    }
}
