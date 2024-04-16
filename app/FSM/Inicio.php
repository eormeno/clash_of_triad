<?php

namespace App\FSM;

class Inicio extends Estado
{
    public function __construct()
    {
        parent::__construct('inicio');
    }
}
