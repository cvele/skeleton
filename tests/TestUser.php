<?php


namespace App\Tests;

use App\Entity\User;

class TestUser extends User
{
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
