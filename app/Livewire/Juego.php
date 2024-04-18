<?php

namespace App\Livewire;

use App\FSM\FSM;
use Livewire\Component;

class Juego extends Component
{

    private $estadosDeJuego = [
        'inicio' => ['Inicio', 0.0, 'buscando_oponente'],
        'buscando_oponente' => ['Buscando oponente', 15.0, 'oponente'],
        'oponente' => ['Oponente encontrado', 4.0, 'ronda'],
        'ronda' => ['Ronda', 3.0, 'juega'],
        'juega' => ['Haz tu jugada', 3.0, 'calcular'],
        'calcular' => ['Calculando resultado', 2.0, 'fin_ronda'],
        'fin_ronda' => ['Resultado de la ronda', 2.0, 'ronda', 'fin_juego'],
        'fin_juego' => ['Fin del juego', 0.0, 'fin'],
        'fin' => ['Fin', 0.0, 'inicio'],
    ];

    public float $temporizador = 0.0;

    public string $remainingTime = '0';

    public $estadoActual = 'inicio';

    public $estadoDeJuego;

    public $jugador = '';

    public $choice = -1;

    public int $ronda = 0;

    private FSM $fsm;

    public function __construct()
    {
        $this->fsm = FSM::crear();
        $this->fsm
            ->estadoInicial()
            ->decisión('¿Existe oponente?')
            ->siguientes([
                'buscando oponente',
                'oponente encontrado'
            ])
            ->estado('buscando oponente')->duración(15.0)
            ->siguiente('oponente encontrado')->duración(4.0)
            ->siguiente('mostrar número ronda')->duración(3.0)
            ->siguiente('pedir jugada')->interactivo()
            ->siguiente('calcular')
            ->siguiente('mostrar resultado ronda')->duración(2.0)
            ->siguiente('incrementar ronda')
            ->decisión('¿Es fin de juego?')
            ->siguientes([
                'mostrar resultado juego',
                'mostrar número ronda'
            ])
            ->estado('mostrar resultado juego')->duración(4.0)
            ->fin();

        $this->estadoDeJuego = $this->estadosDeJuego['inicio'];
    }

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
        $this->estadoActual = $this->estadoDeJuego[2];
        $this->estadoDeJuego = $this->estadosDeJuego[$this->estadoActual];
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
