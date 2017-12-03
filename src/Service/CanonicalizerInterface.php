<?php

namespace App\Service;

interface CanonicalizerInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function canonicalize($string);
}
