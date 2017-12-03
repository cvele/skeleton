<?php

namespace App\Service\Security;

use App\Event\UserEvent;
use App\Event\EventRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Executes manipulations on the users.
 *
 * @author Vladimir Cvetic <vladimir@ferdinand.rs>
 */
class UserManipulator
{
    /**
     * User manager.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UserManipulator constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $dispatcher
     * @param RequestStack             $requestStack
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $dispatcher, RequestStack $requestStack)
    {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function createUserObject()
    {
        return $this->userManager->createUser();
    }

    /**
     * Creates a user and returns it.
     *
     * @param UserInterface|array $user
     *
     * @return UserInterface
     */
    public function create($user) : UserInterface
    {
        if (!$user instanceof UserInterface) {
            $userClass= $this->userManager->getClass();
            /** @var UserInterface **/
            $user = $userClass::fromArray($user);
        }
        
        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_PRE_CREATED, $event);

        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_POST_CREATED, $event);

        return $user;
    }

    /**
     * Activates the given user.
     *
     * @param UserInterface|string $user
     */
    public function activate($user)
    {
        if (!$user instanceof UserInterface) {
            $user = $this->findUserByEmailOrThrowException($user);
        }

        $user->setActive(true);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_PRE_ACTIVATE, $event);

        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_POST_ACTIVATE, $event);
    }

    /**
     * Deactivates the given user.
     *
     * @param UserInterface|string $user
     */
    public function deactivate($user)
    {
        if (!$user instanceof UserInterface) {
            $user = $this->findUserByEmailOrThrowException($user);
        }

        $user->setActive(false);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_PRE_DEACTIVATE, $event);

        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_POST_DEACTIVATE, $event);
    }

    /**
     * Changes the password for the given user.
     *
     * @param UserInterface|string $user
     * @param string $password
     */
    public function changePassword($user, $password)
    {
        if (!$user instanceof UserInterface) {
            $user = $this->findUserByEmailOrThrowException($user);
        }

        $user->setPlainPassword($password);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_PRE_PASSWORD_CHANGED, $event);

        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch(EventRegistry::USER_POST_PASSWORD_CHANGED, $event);
    }

    /**
     * Finds a user by his email and throws an exception if we can't find it.
     *
     * @param string $email
     *
     * @throws \InvalidArgumentException When user does not exist
     *
     * @return UserInterface
     */
    private function findUserByEmailOrThrowException($email)
    {
        $user = $this->userManager->findUserByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $email));
        }

        return $user;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
