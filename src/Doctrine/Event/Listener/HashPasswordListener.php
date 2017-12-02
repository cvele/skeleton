<?php

namespace App\Doctrine\Event\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** Encrypt password on update/create. Uses User:class->plainPassword **/
class HashPasswordListener implements EventSubscriber
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
     * @param  LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UserInterface) {
            return;
        }
        $this->encodePassword($entity);
    }

    /**
     * @param  LifecycleEventArgs $args
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UserInterface) {
            return;
        }
        $this->encodePassword($entity);
        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
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

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}
