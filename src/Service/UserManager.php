<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

/** @author Vladimir Cvetic **/
class UserManager
{
    /** @var EntityManagerInterface **/
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $user->setPassword(null);
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
