<?php

namespace App\FSM;

class Estado
{
    private string $nombre;
    private float $duración;
    private string $descripcion;
    private Estado $siguiente;
    private Estado $alternativo;

    public function __construct(string $nombre, float $duración = 0, string $descripcion = '')
    {
        $this->nombre = $nombre;
        $this->duración = $duración;
        $this->descripcion = $descripcion;
    }

    public function siguiente(Estado $siguiente): Estado
    {
        $this->siguiente = $siguiente;
        return $this;
    }

    public function alternativo(Estado $alternativo): Estado
    {
        $this->alternativo = $alternativo;
        return $this;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDuración(): float
    {
        return $this->duración;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getSiguiente(): Estado
    {
        return $this->siguiente;
    }

    public function getAlternativo(): Estado
    {
        return $this->alternativo;
    }
}
