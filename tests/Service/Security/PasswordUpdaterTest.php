<?php

namespace App\Tests\Service\Security;

use App\Tests\TestUser;
use App\Service\Security\PasswordUpdater;
use PHPUnit\Framework\TestCase;

class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater
     */
    private $updater;
    private $encoderFactory;

    protected function setUp()
    {
        $this->encoderFactory = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface')->getMock();
        $this->updater = new PasswordUpdater($this->encoderFactory);
    }

    public function testUpdatePassword()
    {
        $encoder = $this->getMockPasswordEncoder();
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isType('string'))
            ->will($this->returnValue('encodedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNotNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordWithBCrypt()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder')
            ->disableOriginalConstructor()
            ->getMock();
        $user = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isNull())
            ->will($this->returnValue('encodedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithoutPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    private function getMockPasswordEncoder()
    {
        return $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')->getMock();
    }
}
