<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Service\Security\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var FakerGenerator **/
    private $fakerFactory;

    public function __construct(UserManager $userManager)
    {
        /** @var FakerGenerator **/
        $this->faker = FakerFactory::create();
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userManager->findUsers();
        foreach($users as $user) {
            $address = new Address;
            $address->setAddress($this->faker->streetName);
            $address->setAddress2($this->faker->secondaryAddress);
            $address->setCity($this->faker->city);
            $address->setZip($this->faker->postcode);
            $address->setUser($this->getReference("user-reference-" . $user->getEmail()));
            $address->setTenant($user->getTenant());
            $manager->persist($address);
        }
        $manager->flush();
    }

    /** @inheritDoc **/
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
