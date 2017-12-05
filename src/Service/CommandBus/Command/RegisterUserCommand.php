<?php

namespace App\Service\CommandBus\Command;

use Symfony\Component\Security\Core\User\UserInterface;

class RegisterUserCommand
{
    /** @var UserInterface * */
    private $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
