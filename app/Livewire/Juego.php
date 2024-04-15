<?php

namespace App\Livewire;

use Livewire\Component;

class Juego extends Component
{

    private $estadosDeJuego = [
        'inicio'    => ['Inicio', 0.0, 'buscar'],
        'buscar'    => ['Buscando oponente', 15.0, 'oponente'],
        'oponente'  => ['Oponente encontrado', 4.0, 'ronda'],
        'ronda'     => ['Ronda', 3.0, 'juega'],
        'juega'     => ['Haz tu jugada', 3.0, 'calcular'],
        'calcular'  => ['Calculando resultado', 2.0, 'fin_ronda'],
        'fin_ronda' => ['Resultado de la ronda', 2.0, 'ronda', 'fin_juego'],
        'fin_juego' => ['Fin del juego', 0.0, 'fin'],
        'fin'       => ['Fin', 0.0, 'inicio'],
    ];

    public float $temporizador = 0.0;

    public string $remainingTime = '0';

    public $estadoActual = 'inicio';

    public $estadoDeJuego;

    public $jugador = '';

    public $choice = -1;

    public int $ronda = 0;

    public function mount()
    {
        $this->jugador = auth()->user()->name;
        $this->estadoDeJuego = $this->estadosDeJuego['inicio'];
        $this->nextState();
    }

    public function updateState()
    {
        $this->temporizador += $this->getDeltaTime();

        if ($this->temporizador >= $this->estadoDeJuego[1]) {
            $this->temporizador = 0.0;
            $this->resetTime();
            $this->nextState();
        }

        $this->remainingTime = number_format($this->estadoDeJuego[1] - $this->temporizador, 0);

        $this->storeTime();
    }

    public function nextState()
    {
        $siguienteEstado = $this->estadoDeJuego[2];
        $this->estadoDeJuego = $this->estadosDeJuego[$siguienteEstado];
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

    public function resetTime()
    {
        session()->put('time', 0.0);
    }

    /**
     * This function returns the delta time between the current time and the time stored in the
     * session in seconds.
     */
    public function getDeltaTime(): float
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
