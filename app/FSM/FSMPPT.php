<?php

namespace App\FSM;

class FSMPPT extends FSM
{
    public function __construct()
    {
        parent::__construct();
        $this->inicio->
            fabricarSiguiente('buscando_oponente', 15.0)->
            fabricarSiguiente('oponente_encontrado', 4.0)->
            fabricarSiguiente('ronda', 3.0)->
            fabricarSiguiente('juega', 3.0)->
            fabricarSiguiente('calcular', 2.0)->
            fabricarSiguiente('resultado_ronda', 2.0)->
            siguiente('ronda')->
            fabricarAlternativo('resultado_juego');
    }
}
