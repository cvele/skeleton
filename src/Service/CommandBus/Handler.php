<?php

namespace App\Service\CommandBus;

use Doctrine\ORM\EntityManagerInterface;
use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
class Handler
{
    /** @var EntityManagerInterface * */
    private $entityManager;

    /** @var EventDispatcherInterface * */
    private $dispatcher;

    /** @var CommandBus * */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(CommandBus $commandBus, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        /* @var CommandBus **/
        $this->commandBus = $commandBus;

        /* @var EntityManagerInterface **/
        $this->entityManager = $entityManager;

        /* @var EventDispatcherInterface **/
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @return CommandBus
     */
    public function getCommandBus(): CommandBus
    {
        return $this->commandBus;
    }
}
