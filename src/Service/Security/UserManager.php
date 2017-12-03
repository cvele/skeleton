<?php

namespace App\Service\Security;

use App\Entity\User;
use App\Service\TokenGeneratorInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
class UserManager implements UserManagerInterface
{
    /** @var PasswordUpdaterInterface **/
    private $passwordUpdater;

    /** @var UserCanonicalFieldsUpdater **/
    private $canonicalFieldsUpdater;

    /** @var TokenGeneratorInterface **/
    private $tokenGenerator;

    /** @var ObjectManager **/
    protected $objectManager;

    /**
     * @param PasswordUpdaterInterface   $passwordUpdater
     * @param UserCanonicalFieldsUpdater $canonicalFieldsUpdater
     * @param TokenGeneratorInterface    $tokenGenerator
     * @param ObjectManager              $om
     */
    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        UserCanonicalFieldsUpdater $canonicalFieldsUpdater,
        TokenGeneratorInterface $tokenGenerator,
        ObjectManager $om)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->tokenGenerator = $tokenGenerator;
        $this->objectManager = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findUsers()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->objectManager->refresh($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken(
                $this->getTokenGenerator()->generate()
            );
        }

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(UserInterface $user)
    {
        $this->passwordUpdater->hashPassword($user);
    }

    /**
     * @return TokenGeneratorInterface
     */
    protected function getTokenGenerator() : TokenGeneratorInterface
    {
        return $this->tokenGenerator;
    }

    /**
     * @return PasswordUpdaterInterface
     */
    protected function getPasswordUpdater() : PasswordUpdaterInterface
    {
        return $this->passwordUpdater;
    }

    /**
     * @return UserCanonicalFieldsUpdater
     */
    protected function getCanonicalFieldsUpdater() : UserCanonicalFieldsUpdater
    {
        return $this->canonicalFieldsUpdater;
    }
    /**
     * @return ObjectRepository
     */
    protected function getRepository() : ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return User::class;
    }

}
