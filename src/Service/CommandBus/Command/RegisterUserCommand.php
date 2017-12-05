<?php

namespace App\Service\CommandBus\Command;

use Symfony\Component\Security\Core\User\UserInterface;

class RegisterUserCommand
{
    /** @var UserInterface **/
    private $user;

    public function __construct(UserInterface $user)
    {
        /** @var UserInterface **/
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser() : UserInterface
    {
        return $this->user;
    }
}
