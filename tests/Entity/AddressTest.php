<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Tenant;
use App\Entity\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase {

    /**
     * @var Address
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Address();
    }

    /**
     * @dataProvider addressDataProvider
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

    public function addressDataProvider()
    {

        $data = [
            [[
                'createdAt' => new \DateTime(),
                'updatedAt' => new \DateTime(),
                'tenant' => new Tenant(),
                'city' => 'Belgrade',
                'address' => 'address 1',
                'address2' => 'address 2',
                'zip' => '11000',
                'user' => new User,
            ]]
        ];

        return $data;
    }
}
