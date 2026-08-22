<?php

namespace App\Exceptions;

use RuntimeException;

final class StaleTicket extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The ticket changed after it was read. Reload it and retry.');
    }
}
