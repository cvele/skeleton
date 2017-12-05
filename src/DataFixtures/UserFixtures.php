<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\CommandBus\Command\RegisterUserCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use League\Tactician\CommandBus;

class UserFixtures extends Fixture
{
    /** @var FakerGenerator * */
    private $fakerFactory;

    /** @var CommandBus * */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->faker = FakerFactory::create();
        $this->commandBus = $commandBus;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; ++$i) {
            $safeEmail = $this->faker->unique()->safeEmail;
            $user = new User();
            $user->setEmail($safeEmail);
            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            $user->setPlainPassword($this->faker->password);
            $user->setActive($this->faker->boolean($chanceOfGettingTrue = 90));
            $user->setConfirmed($this->faker->boolean($chanceOfGettingTrue = 80));
            $user->setTenant($this->getReference('tenant-'.rand(1, 2)));
            $command = new RegisterUserCommand($user);
            $this->commandBus->handle($command);
            $this->addReference('user-reference-'.$safeEmail, $user);
        }
    }

    /** {@inheritdoc} **/
    public function getDependencies()
    {
        return [
            TenantFixtures::class,
        ];
    }
}
