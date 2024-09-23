<?php

namespace App\Helper;

trait ClearableTrait
{

    private bool $clear = false;

    /**
     * @param bool $clear
     */
    public function setClear(bool $clear): void
    {
        $this->clear = $clear;
    }

    /**
     * @return bool
     */
    public function isClear(): bool
    {
        return $this->clear;
    }

}
