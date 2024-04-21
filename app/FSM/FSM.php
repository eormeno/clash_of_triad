<?php

namespace App\FSM;
use Livewire\Component;

class FSM
{
    const FRECUENCIA = 1000; // 100ms = 0.1s
    private $estados = [];

    private Estado $estadoActual;

    private Component $component;

    public function __construct(Component $component)
    {
        $this->component = $component;
        $this->estadoActual = $this->estadoInicial();
    }

    public function setEstadoActual(string $nombre, float $restante): void
    {
        $this->estadoActual = $this->estado($nombre);
        $this->estadoActual->setRestante($restante);
    }

    public function estadoInicial(): Estado
    {
        $estado = $this->estado('inicio')->setAsInitial();
        return $estado;
    }

    public function estadoFinal(): Estado
    {
        $fin = $this->estado('fin')->setAsFinal();
        return $fin;
    }

    public function estado(string $nombre): Estado
    {
        if (array_key_exists($nombre, $this->estados)) {
            return $this->estados[$nombre];
        }
        $estado = new Estado($this, $nombre);
        $this->estados[$nombre] = $estado;
        return $estado;
    }

    public function actualizar(float $deltaTime): Estado
    {
        $estado = $this->estadoActual;
        // Acá se debería actualizar el estado actual hasta que se agote el tiempo pero
        // se debería dividir el delta time en la frecuencia
//        do {
            $estado = $this->actualizarEstado($estado, self::FRECUENCIA);
//            $this->log('Restante: ' . $estado->getRestante());
//        } while ($estado->getRestante() > 0);
        $this->estadoActual = $estado;
        return $this->estadoActual;
    }

    private function actualizarEstado(Estado $estado, float $deltaTime): Estado
    {
        $nuevoEstado = $estado->actualizar($deltaTime);
        //$this->log('Estado actual: ' . $nuevoEstado->getNombre());
        if ($nuevoEstado !== $estado) {
            //$this->log('Saliendo de ' . $estado->getNombre());
            $estado->salir();
            $nuevoEstado->entrar();
        }
        return $nuevoEstado;
    }

    public function log(string $mensaje, $nivel = 'info')
    {
        $this->component->log($mensaje);
    }
}
