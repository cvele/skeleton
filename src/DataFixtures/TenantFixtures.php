<?php

namespace App\DataFixtures;

use App\Entity\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TenantFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tenant = new Tenant();
        $tenant->setName('Tenant A');
        $manager->persist($tenant);
        $this->addReference('tenant-1', $tenant);

        $tenant = new Tenant();
        $tenant->setName('Tenant B');
        $manager->persist($tenant);
        $this->addReference('tenant-2', $tenant);

        $manager->flush();
    }
}
