<?php

namespace App\Livewire;

use Livewire\Component;

class Juego extends Component
{

    private $estadosDeJuego = [
        'Buscando oponente',
        'Elige tu jugada',
        'Esperando a tu oponente',
        'Resultado de la partida',
    ];

    public $indiceEstadoActual = 0;

    public $estadoDeJuego;

    public $jugador = '';

    public $choice = -1;

    public function mount()
    {
        $this->jugador = auth()->user()->name;
        $this->updateState();
    }

    public function updateState()
    {
        $countStates = count($this->estadosDeJuego);
        if ($this->indiceEstadoActual > $countStates - 1) {
            $this->indiceEstadoActual = 0;
        }
        $this->estadoDeJuego = $this->estadosDeJuego[$this->indiceEstadoActual];
        $this->indiceEstadoActual++;
    }

    public function rock()
    {
        $this->choice = 0;
    }

    public function paper()
    {
        $this->choice = 1;
    }

    public function scissors()
    {
        $this->choice = 2;
    }

    public function render()
    {
        return view('livewire.juego');
    }
}
