<?php

namespace App\Service;

class Calculs
{

    public function htToTtc($ht): int
    {
        return $ht / 100 * 1.2;
    }

    public function ttcToHt($ttc): int
    {
        return $ttc * 100 / 1.2;
    }
}