<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Tenant;
use PHPUnit\Framework\TestCase;

class TenantTest extends TestCase {

    /**
     * @var Tenant
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Tenant();
    }

    /**
     * @dataProvider tenantDataProvider
     */
    public function testGetterAndSetter($data)
    {
        foreach($data as $property => $value) {
            $setter = "set" . ucfirst($property);
            $getter = "get" . ucfirst($property);
            $this->object->$setter($value);
            $this->assertEquals($value, $this->object->$getter());
        }

        $this->assertEquals(null, $this->object->getId());
    }

    public function testUserCollection()
    {
        $userA = User::fromArray(['firstname' => 'A']);
        $userB = User::fromArray(['firstname' => 'B']);
        $this->object->addUser($userA);
        $this->object->addUser($userB);

        $this->assertTrue($this->object->getUsers()->contains($userA));
        $this->assertTrue($this->object->getUsers()->contains($userB));

        $this->object->removeUser($userB);
        $this->assertFalse($this->object->getUsers()->contains($userB));
    }

    public function tenantDataProvider()
    {
        $data = [
            [[
                'createdAt' => new \DateTime(),
                'updatedAt' => new \DateTime(),
                'name' => 'tenant A',
            ]],
        ];

        return $data;
    }
}
