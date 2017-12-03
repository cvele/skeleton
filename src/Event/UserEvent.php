<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
class UserEvent extends Event
{
    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * UserEvent constructor.
     *
     * @param UserInterface $user
     * @param Request|null  $request
     */
    public function __construct(UserInterface $user, Request $request = null)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
