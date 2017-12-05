<?php

namespace App\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Entity\Traits;

class Entity
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Adds fromArray method, for easy hydration
     */
    use Traits\EntityHydrationTrait;
}
