<?php

namespace App\Livewire;

use Livewire\Component;

class Juego extends Component
{

    private $estadosDeJuego = [
        ['Buscando oponente', 15.0],
        ['Elige tu jugada', 3.0],
        ['Resultado de la partida', 2.0],
    ];

    public float $temporizador = 0.0;

    public string $remainingTime = '0.00';

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
        $this->temporizador += $this->getDeltaTime();
        $this->remainingTime = number_format($this->estadoDeJuego[1] - $this->temporizador, 1);

        if ($this->temporizador >= $this->estadoDeJuego[1]) {
            $this->temporizador = 0;
            $this->nextState();
        }
        $this->storeTime();
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

    /**
     * This function stores the curren miliseconds of the server the user's session
     */
    public function storeTime()
    {
        session()->put('time', microtime(true));
    }

    /**
     * This function returns the delta time between the current time and the time stored in the
     * session in seconds.
     */
    public function getDeltaTime() : float
    {
        $currentTime = microtime(true);
        $lastTime = session()->get('time');
        return ($currentTime - $lastTime);
    }

    public function render()
    {
        return view('livewire.juego');
    }
}
