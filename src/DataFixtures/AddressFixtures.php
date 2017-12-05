<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Address;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var FakerGenerator * */
    private $fakerFactory;

    /** @var EntityManagerInterface * */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        /* @var FakerGenerator **/
        $this->faker = FakerFactory::create();
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->em->getRepository(User::class)->find();
        foreach ($users as $user) {
            $address = new Address();
            $address->setAddress($this->faker->streetName);
            $address->setAddress2($this->faker->secondaryAddress);
            $address->setCity($this->faker->city);
            $address->setZip($this->faker->postcode);
            $address->setUser($this->getReference('user-reference-'.$user->getEmail()));
            $address->setTenant($user->getTenant());
            $manager->persist($address);
        }
        $manager->flush();
    }

    /** {@inheritdoc} **/
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
