<?php

namespace App\FSM;

class FSM {

    private $estados = [];

    protected final Inicio $inicio;

    protected final Fin $fin;

    protected Estado $estadoActual;

    private float $temporizador = 0;

    public function __construct() {
        $this->inicio = Estado::fabricarInicio();
        $this->fin = Estado::fabricarFin();
    }

    public function registerTime() {
        session()->put('time', microtime(true));
    }

    public function resetTime()
    {
        session()->put('time', 0.0);
    }

    private function getDeltaTime(): float
    {
        $currentTime = microtime(true);
        $lastTime = session()->get('time');
        return ($currentTime - $lastTime);
    }
}
