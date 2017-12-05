<?php

namespace App\Tests\Form\Type;

use App\Entity\Tenant;
use App\Form\Type\TenantType;

class TenantTypeTest extends BaseTypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => '123456',
        ];

        $object = Tenant::fromArray($formData);
        $form = $this->factory->create(TenantType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf(Tenant::class, $object);
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
