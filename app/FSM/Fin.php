<?php

namespace App\FSM;

class Fin extends Estado
{
    public function __construct()
    {
        parent::__construct('fin');
    }
}
