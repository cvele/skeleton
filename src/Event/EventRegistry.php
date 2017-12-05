<?php

namespace App\Event;

/** Registry of all dispatched events **/
class EventRegistry
{
    /** @var string **/
    const USER_POST_CREATED = 'user.post.created';
    /** @var string **/
    const USER_POST_ACTIVATE = 'user.post.activate';
    /** @var string **/
    const USER_POST_DEACTIVATE = 'user.post.deactivate';
    /** @var string **/
    const USER_POST_PASSWORD_CHANGED = 'user.post.password_changed';

}
