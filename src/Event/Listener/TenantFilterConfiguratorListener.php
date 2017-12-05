<?php

namespace App\Event\Listener;

use App\Entity\Tenant;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TenantFilterConfiguratorListener
{
    /** @var ObjectManager * */
    protected $om;

    /** @var AuthorizationChecker * */
    private $authorizationChecker;

    /** @var TokenStorageInterface * */
    private $tokenStorage;

    public function __construct(ObjectManager $om, AuthorizationChecker $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        $this->om = $om;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelRequest()
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return false;
        }

        $currentUser = $token->getUser();
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        if ($currentUser->getTenant() instanceof Tenant) {
            try {
                $filter = $this->om->getFilters()->enable('tenant_filter');
            } catch (\InvalidArgumentException $e) {
                return false;
            }
            $filter->setParameter(Tenant::class, $tenant);
        }
    }
}
