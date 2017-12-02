<?php

namespace App\Event;

/** Registry of all dispatched events **/
class EventRegistry
{
    /**
     * Dispatched from \App\Service\Security\UserManager::save
     * @var string
     */
    const USER_PRE_SAVE = 'user.pre.save';
}
