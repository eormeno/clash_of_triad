<?php

namespace App\FSM;

class Estado
{
    private FSM $fsm;
    private string $nombre;
    private float $duración;
    private array $siguientes = [];

    public function __construct(FSM $fsm, string $nombre, float $duración = 0)
    {
        $this->fsm = $fsm;
        $this->nombre = $nombre;
        $this->duración = $duración;
    }

    public function siguiente(string $nombre, float $duración = 0): Estado
    {
        $siguiente = $this->fsm->crearOBuscar($nombre, $duración);
        $this->siguientes[$nombre] = $siguiente;
        return $siguiente;
    }

    public function decisión(string $nombre): Estado
    {
        $siguiente = $this->fsm->crearOBuscar($nombre);
        $this->siguientes[$nombre] = $siguiente;
        return $siguiente;
    }

    public function siguientes(array $siguientes): Estado
    {
        foreach ($siguientes as $nombre) {
            $this->siguiente($nombre);
        }
        return $this;
    }

    public function crearOBuscar(string $nombre, float $duración = 0): Estado
    {
        return $this->fsm->crearOBuscar($nombre, $duración);
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDuración(): float
    {
        return $this->duración;
    }
}
