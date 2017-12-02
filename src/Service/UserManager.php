<?php

namespace App\Service;

use App\Entity\User;
use App\Event\UserEvent;
use App\Event\EventRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @author Vladimir Cvetic **/
class UserManager
{
    /** @var EntityManagerInterface **/
    private $em;

    /** @var EventDispatcherInterface **/
    private $dispatcher;

    /**
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return UserInterface
     */
    public function createUser() : UserInterface
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }
    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function save($user) : UserInterface
    {
        $event = new UserEvent($user);
        $this->dispatcher->dispatch(EventRegistry::USER_PRE_SAVE, $event);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /** returns class name of user entity **/
    public function getClass()
    {
        return User::class;
    }
}
