<?php

namespace App\FSM;

use Livewire\Component;

class FSM
{
    const MAXIMUM_DELTA_TIME = 10000; // ms
    const UPDATE_INTERVAL = 1000; // ms
    private $estados = [];
    private $variables = [];
    private Estado $estadoActual;
    private Component $component;

    public function __construct(Component $component)
    {
        $this->component = $component;
        $this->estadoActual = $this->estadoInicial();
    }

    public function setEstadoActual(string $nombre, float $restante, array $variables): void
    {
        $this->estadoActual = $this->estado($nombre);
        $this->estadoActual->setRestante($restante);
        $this->variables = $variables;
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
        // Si el tiempo es menor o igual a 1 no se actualiza el estado. Esto es para evitar
        // que se actualice el estado en cada renderizado de Livewire. Lo cual puede suceder
        // si el intervalo es muy corto en relación a la velocidad de la red o de las capacidades
        // del servidor o del cliente.
        if ($deltaTime <= 1) {
            return $this->estadoActual;
        }
        if ($deltaTime > self::MAXIMUM_DELTA_TIME) {
            $deltaTime = self::MAXIMUM_DELTA_TIME;
        }
        $estado = $this->estadoActual;
        // $this->log('$deltaTime = ' . $deltaTime . ' ms' . ' $estado = ' . $estado->getNombre());
        while ($deltaTime > self::UPDATE_INTERVAL) {
            $estado = $this->actualizarEstado($estado, self::UPDATE_INTERVAL);
            $deltaTime -= self::UPDATE_INTERVAL;
        }
        $estado = $this->actualizarEstado($estado, $deltaTime);
        $this->estadoActual = $estado;
        return $this->estadoActual;
    }

    private function actualizarEstado(Estado $estado, float $deltaTime): Estado
    {
        $nuevoEstado = $estado->actualizar($deltaTime);
        if ($nuevoEstado !== $estado) {
            $this->log("{$estado->getNombre()} -> {$nuevoEstado->getNombre()}");
            $estado->salir();
            $nuevoEstado->entrar();
        }
        return $nuevoEstado;
    }

    public function setValue(string $variable_name, $value = null): void
    {
        $this->variables[$variable_name] = $value;
    }

    public function getValue(string $variable_name)
    {
        if (!array_key_exists($variable_name, $this->variables)) {
            return null;
        }
        return $this->variables[$variable_name];
    }

    public function hasValue(string $variable_name): bool
    {
        return array_key_exists($variable_name, $this->variables) && $this->variables[$variable_name] !== null;
    }

    public function log(string $mensaje, $nivel = 'info')
    {
        $this->component->log($mensaje);
    }
}
