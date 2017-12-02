<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The user.saved event is dispatched each time an users is created or updated
 * in the system.
 */
class UserEvent extends Event
{
    /** @var UserInterface **/
    protected $user;

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
    public function getUser() : UserInterface
    {
        return $this->user;
    }
}
