<?php

namespace App\FSM;

class Estado
{
    private string $nombre;
    private float $duración;
    private string $descripcion;
    private Estado $siguiente;
    private Estado $alternativo;

    private function __construct(string $nombre, float $duración = 0, string $descripcion = '')
    {
        $this->nombre = $nombre;
        $this->duración = $duración;
        $this->descripcion = $descripcion;
    }

    public static function fabricarInicio(): Estado
    {
        return new Estado('inicio');
    }

    public static function fabricarFin(): Estado
    {
        return new Estado('fin');
    }

    public function fabricarSiguiente(string $nombre, float $duración = 0, string $descripcion = '') : Estado
    {
        $this->siguiente = new Estado($nombre, $duración, $descripcion);
        return $this->siguiente;
    }

    public function fabricarAlternativo(string $nombre, float $duración = 0, string $descripcion = '') : Estado
    {
        $this->alternativo = new Estado($nombre, $duración, $descripcion);
        return $this->alternativo;
    }

    public function siguiente(string $siguiente): Estado
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
