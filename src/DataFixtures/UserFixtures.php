<?php

namespace App\DataFixtures;

use App\Service\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class UserFixtures extends Fixture
{
    /** @var UserManager **/
    private $userManager;

    /** @var FakerGenerator **/
    private $fakerFactory;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        /** @var UserManager **/
        $this->userManager = $userManager;
        /** @var FakerGenerator **/
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 50; $i++) {
            $safeEmail = $this->faker->unique()->safeEmail;
            $user = $this->userManager->createUser();
            $user->setEmail($safeEmail);
            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            /** Will be hashed by App\Doctrine\Event\Listener\HashPasswordListener **/
            $user->setPlainPassword($this->faker->password);
            $user->setIsActive($this->faker->boolean($chanceOfGettingTrue = 90));
            $this->addReference($safeEmail, $user);
            $this->userManager->save($user);
        }
    }
}
