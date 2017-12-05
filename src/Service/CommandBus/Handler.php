<?php

namespace App\Service\CommandBus;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
class Handler
{
    /** @var EntityManagerInterface **/
    private $entityManager;

    /** @var EventDispatcherInterface **/
    private $dispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        /** @var EntityManagerInterface **/
        $this->entityManager = $entityManager;

        /** @var EventDispatcherInterface **/
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher() : EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}
