<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Tenant;
use App\Entity\Address;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

    /**
     * @var User
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new User();
    }

    /**
     * @dataProvider userDataProvider
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
        $this->assertEquals(['ROLE_USER'], $this->object->getRoles());
    }

    public function testAddressCollection()
    {
        $addressA = Address::fromArray(['city' => 'A']);
        $addressB = Address::fromArray(['city' => 'B']);
        $this->object->addAddress($addressA);
        $this->object->addAddress($addressB);

        $this->assertTrue($this->object->getAddresses()->contains($addressA));
        $this->assertTrue($this->object->getAddresses()->contains($addressB));

        $this->object->removeAddress($addressB);
        $this->assertFalse($this->object->getAddresses()->contains($addressB));
    }

    public function userDataProvider()
    {

        $data = [
            [[
                'createdAt' => new \DateTime(),
                'updatedAt' => new \DateTime(),
                'tenant' => new Tenant(),
                'firstname' => 'John',
                'lastname' => 'Doe',
                'password' => 'password',
                'plainPassword' => 'plainPassword',
                'salt' => 'salt',
                'confirmationToken' => '1234',
                'confirmed' => true,
                'active' => true,
            ]],
            [[
                'createdAt' => new \DateTime(),
                'updatedAt' => new \DateTime(),
                'tenant' => new Tenant(),
                'firstname' => 'John',
                'lastname' => 'Doe',
                'password' => 'password',
                'plainPassword' => 'plainPassword',
                'salt' => null,
                'confirmationToken' => null,
                'confirmed' => false,
                'active' => false,
            ]],
        ];

        return $data;
    }
}
