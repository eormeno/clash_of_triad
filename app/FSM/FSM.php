<?php

namespace App\FSM;

class FSM {

    private $estados = [];

    protected Estado $estadoActual;

    private float $temporizador = 0;

    public static function crear(): FSM {
        $fsm = new FSM();
        $fsm->resetTime();
        return $fsm;
    }

    public function estadoInicial(): Estado {
        $estado = $this->estado('inicio')->esInicial();
        $this->estadoActual = $estado;
        return $estado;
    }

    public function estadoFinal(): Estado
    {
        $fin = $this->estado('fin')->esFinal();
        return $fin;
    }

    public function estado(string $nombre): Estado {
        if (array_key_exists($nombre, $this->estados)) {
            return $this->estados[$nombre];
        }
        $estado = new Estado($this, $nombre);
        $this->estados[$nombre] = $estado;
        return $estado;
    }

    public function actualizar(): Estado {
        $estado = $this->estadoActual;
        $nuevoEstado = $estado->actualizar($this->getDeltaTime());
        if ($nuevoEstado !== null && $nuevoEstado !== $this->estadoActual) {
            $estado->salir();
            $this->estadoActual = $nuevoEstado;
            $this->estadoActual->entrar();
        }
        $this->registerTime();
        return $this->estadoActual;
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
