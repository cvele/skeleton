<?php

namespace App\Event\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** Encrypt password on update/create. Uses User:class->plainPassword **/
class HashPasswordListener
{
    /** @var UserPasswordEncoderInterface $passwordEncoder **/
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onUserPreSave(Event $event)
    {
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        $this->encodePassword($user);
    }

    /**
     * @param UserInterface $user
     */
    private function encodePassword(UserInterface $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($encoded);
        $user->eraseCredentials();
    }

}
