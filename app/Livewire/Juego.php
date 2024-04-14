<?php

namespace App\Livewire;

use Livewire\Component;

class Juego extends Component
{

    private $estadosDeJuego = [
        ['Buscando oponente', 15],
        ['Elige tu jugada', 3],
        ['Resultado de la partida', 2],
    ];

    public $temporizador = 0;

    public $indiceEstadoActual = 0;

    public $estadoDeJuego;

    public $jugador = '';

    public $choice = -1;

    public function mount()
    {
        $this->jugador = auth()->user()->name;
        $this->indiceEstadoActual = -1;
        $this->nextState();
    }

    public function updateState()
    {
        $this->temporizador++;
        if ($this->temporizador >= $this->estadoDeJuego[1]) {
            $this->temporizador = 0;
            $this->nextState();
        }
    }

    public function nextState()
    {
        $this->indiceEstadoActual++;
        $countStates = count($this->estadosDeJuego);
        if ($this->indiceEstadoActual > $countStates - 1) {
            $this->indiceEstadoActual = 0;
        }
        $this->estadoDeJuego = $this->estadosDeJuego[$this->indiceEstadoActual];
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
