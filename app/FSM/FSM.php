<?php

namespace App\FSM;

class FSM {

    private $estados = [];

    protected Estado $inicio;

    protected Estado $fin;

    protected Estado $estadoActual;

    private float $temporizador = 0;

    public static function crear(): FSM {
        $fsm = new FSM();
        $fsm->inicio= $fsm->crearOBuscar('inicio');
        $fsm->fin = $fsm->crearOBuscar('fin');
        $fsm->estadoActual = $fsm->inicio;
        return $fsm;
    }

    public function crearOBuscar(string $nombre, float $duración = 0): Estado {
        if (array_key_exists($nombre, $this->estados)) {
            $estado_actual = $this->estados[$nombre];
            if ($duración > 0) {
                $estado_actual->duración = $duración;
            }
            return $this->estados[$nombre];
        }
        $estado = new Estado($this, $nombre, $duración);
        $this->estados[$nombre] = $estado;
        return $estado;
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
