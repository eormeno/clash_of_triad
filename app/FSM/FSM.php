<?php

namespace App\FSM;

class FSM {
    const FRECUENCIA = 0.1; // 100ms = 0.1s
    private $estados = [];

    private Estado $estadoActual;

    public static function crear(): FSM {
        $fsm = new FSM();
        $fsm->estadoActual = $fsm->estadoInicial();
        return $fsm;
    }

    public function estadoInicial(): Estado {
        $estado = $this->estado('inicio')->setAsInitial();
        return $estado;
    }

    public function estadoFinal(): Estado
    {
        $fin = $this->estado('fin')->setAsFinal();
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

    public function actualizar(float $deltaTime): Estado {
        $estado = $this->estadoActual;
        do {
            $estado = $this->actualizarEstado($estado, self::FRECUENCIA);
        } while ($estado->getRestante() > 0);

        $this->estadoActual = $estado;

        return $this->estadoActual;
    }

    private function actualizarEstado(Estado $estado, float $deltaTime): Estado {
        $nuevoEstado = $estado->actualizar($deltaTime);
        if ($nuevoEstado !== $estado) {
            $estado->salir();
            $nuevoEstado->entrar();
        }
        return $nuevoEstado;
    }
}
