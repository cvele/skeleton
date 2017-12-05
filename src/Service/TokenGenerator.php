<?php

namespace App\Service;

/** @author Vladimir Cvetic <vladimir@ferdinand.rs> **/
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
