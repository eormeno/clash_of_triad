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
        $fsm->inicio= new Estado('inicio');
        $fsm->estados[$fsm->inicio->getNombre()] = $fsm->inicio;
        $fsm->fin = new Estado('fin');
        $fsm->estados[$fsm->fin->getNombre()] = $fsm->fin;
        $fsm->estadoActual = $fsm->inicio;
        return $fsm;
    }

    public function siguiente(string $origen, string $destino, string $alternativo = null): FSM {
        $origen = $this->estados[$origen];
        $destino = $this->estados[$destino];
        $origen->siguiente($destino);
        if ($alternativo) {
            $alternativo = $this->estados[$alternativo];
            $origen->alternativo($alternativo);
        }
        return $this;
    }

    public function crearEstado(string $nombre, float $duración = 0): FSM {
        if (array_key_exists($nombre, $this->estados)) {
            throw new \Exception("El estado $nombre ya existe");
        }
        $estado = new Estado($nombre, $duración);
        $this->estados[$nombre] = $estado;
        return $this;
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
