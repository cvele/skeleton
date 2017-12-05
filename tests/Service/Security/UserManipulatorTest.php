<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use App\Entity\User;
use App\Event\EventRegistry;
use App\Service\CanonicalizerInterface;
use App\Service\Security\UserManager;
use App\Service\Security\UserManipulator;
use App\Service\Security\PasswordUpdaterInterface;
use App\Service\Security\UserCanonicalFieldsUpdater;
use App\Tests\TestUser;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use App\Service\TokenGeneratorInterface;

class UserManipulatorTest extends TestCase
{
    public function testCreate()
    {
        $userManagerMock = $this->getUserManagerMock();
        $user = new TestUser();

        $password = 'test_password';
        $email = 'test@email.org';
        $active = true; // it is enabled

        $userManagerMock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($user));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_CREATED, true);

        $requestStackMock = $this->getRequestStackMock();

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        $user = $manipulator->create([
            'password' => $password,
            'email' => $email,
            'active' => $active
        ]);

        $this->assertSame($password, $user->getPassword());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($active, $user->getActive());
    }


    public function testActivateWithValidEmail()
    {
        $userManagerMock = $this->getUserManagerMock();

        $email = 'test@primer.com';
        $user = new TestUser();
        $user->setEmail($email);
        $user->setActive(false);

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue($user))
            ->with($this->equalTo($email));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_ACTIVATE, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($email);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame(true, $user->getActive());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testActivateWithInvalidEmail()
    {
        $userManagerMock = $this->getUserManagerMock();
        $invalidEmail = 'invalid_email';

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidEmail));

        $userManagerMock->expects($this->never())->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_ACTIVATE, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($invalidEmail);
    }

    public function testDeactivateWithValidEmail()
    {
        $userManagerMock = $this->getUserManagerMock();

        $email = 'test@primer.com';
        $user = new TestUser();
        $user->setEmail($email);
        $user->setActive(true);

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue($user))
            ->with($this->equalTo($email));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_DEACTIVATE, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($email);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame(false, $user->getActive());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeactivateWithInvalidEmail()
    {
        $userManagerMock = $this->getUserManagerMock();
        $invalidEmail = 'invalid';

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidEmail));

        $userManagerMock->expects($this->never())->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_DEACTIVATE, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($invalidEmail);
    }


    public function testChangePasswordWithValidUsername()
    {
        $userManagerMock = $this->getUserManagerMock();

        $user = new TestUser();
        $email = 'test@primer.com';
        $password = 'test_password';
        $oldpassword = 'old_password';

        $user->setEmail($email);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue($user))
            ->with($this->equalTo($email));

        $userManagerMock->expects($this->once())
            ->method('updateUser')
            ->will($this->returnValue($user))
            ->with($this->isInstanceOf(TestUser::class));

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_PASSWORD_CHANGED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($email, $password);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame($password, $user->getPlainPassword());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testChangePasswordWithInvalidUsername()
    {
        $userManagerMock = $this->getUserManagerMock();

        $invalidEmail = 'invalid';
        $password = 'test_password';

        $userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->will($this->returnValue(null))
            ->with($this->equalTo($invalidEmail));

        $userManagerMock->expects($this->never())->method('updateUser');

        $eventDispatcherMock = $this->getEventDispatcherMock(EventRegistry::USER_POST_PASSWORD_CHANGED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($invalidEmail, $password);
    }

    /**
     * @param string $event
     * @param bool   $once
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock($event, $once = true)
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();

        $eventDispatcherMock->expects($once ? $this->once() : $this->never())
            ->method('dispatch')
            ->with($event);

        return $eventDispatcherMock;
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequestStackMock()
    {
        $requestStackMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')->getMock();

        $requestStackMock->method('getCurrentRequest')->willReturn(null);

        return $requestStackMock;
    }

    protected function getUserManagerMock()
    {
        $passwordUpdaterMock = $this->getMockBuilder(PasswordUpdaterInterface::class)->getMock();
        $canonicalizerMock = $this->getMockBuilder(CanonicalizerInterface::class)->getMock();
        $tokenGeneratorMock = $this->getMockBuilder(TokenGeneratorInterface::class)->getMock();
        $objectManagerMock = $this->getMockBuilder(ObjectManager::class)->getMock();

        $canonicalFieldsUpdaterMock = $this->getMockBuilder(UserCanonicalFieldsUpdater::class)
                                        ->setConstructorArgs([
                                            $canonicalizerMock
                                        ])
                                        ->getMock();

        return $this->getMockBuilder(UserManager::class)
                                ->setConstructorArgs([
                                    $passwordUpdaterMock,
                                    $canonicalFieldsUpdaterMock,
                                    $tokenGeneratorMock,
                                    $objectManagerMock
                                ])
                                ->getMock();
    }
}
