<?php

namespace App\Service\CommandBus\Handler;

use App\Entity\User;
use App\Event\UserEvent;
use App\Event\EventRegistry;
use App\Service\CommandBus\Handler;
use App\Service\CommandBus\Command\RegisterUserCommand;
use App\Service\CommandBus\Command\ChangePasswordCommand;
use App\Service\Security\PasswordUpdaterInterface;
use App\Service\Security\UserCanonicalFieldsUpdater;
use App\Service\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;

/** User Handler **/
class UserHandler extends Handler
{
    /** @var PasswordUpdaterInterface **/
    private $passwordUpdater;

    /** @var UserCanonicalFieldsUpdater **/
    private $canonicalFieldsUpdater;

    /** @var TokenGeneratorInterface **/
    private $tokenGenerator;

    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        UserCanonicalFieldsUpdater $canonicalFieldsUpdater,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher
        )
    {
        parent::__construct($entityManager, $dispatcher);
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param RegisterUserCommand $command
     */
    public function handleRegisterUser(RegisterUserCommand $command)
    {
        $user = $command->getUser();
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
        $user->setConfirmationToken(
            $this->tokenGenerator->generate()
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $event = new UserEvent($user);
        $this->getEventDispatcher()->dispatch(EventRegistry::USER_POST_CREATED, $event);

        return $user;
    }

    /**
     * Changes the password for the given user.
     *
     * @param ChangePasswordCommand $command
     */
    public function handleChangePassword(ChangePasswordCommand $command)
    {
        $user = $this->getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $command->getEmail()]);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $email));
        }

        $user->setPlainPassword($command->getPassword());
        $this->passwordUpdater->hashPassword($user);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $event = new UserEvent($user);
        $this->getEventDispatcher()->dispatch(EventRegistry::USER_POST_PASSWORD_CHANGED, $event);
    }

}
