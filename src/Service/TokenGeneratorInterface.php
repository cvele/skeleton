<?php

namespace App\Service;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
