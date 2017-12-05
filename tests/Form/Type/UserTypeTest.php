<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Entity\Tenant;
use App\Form\Type\UserType;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTypeTest extends BaseTypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'tenant' => ['name'=>'primer'],
            'email' => 'test@primer.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'plainPassword' => ['first' => 'pass', 'second' => 'pass'],
            'termsAccepted' => true
        ];

        $object = User::fromArray($formData);
        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());
        $this->assertInstanceOf(UserInterface::class, $object);
        $this->assertInstanceOf(Tenant::class, $object->getTenant());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
