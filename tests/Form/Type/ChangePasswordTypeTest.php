<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;

class ChangePasswordTypeTest extends BaseTypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'current_password' => '123456',
            'plainPassword' => ['first' => 'pass', 'second' => 'pass']
        ];

        $object = User::fromArray($formData);
        $form = $this->factory->create(ChangePasswordType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
