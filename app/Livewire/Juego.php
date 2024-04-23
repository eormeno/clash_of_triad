<?php

namespace App\Livewire;

use App\FSM\FSM;
use App\FSM\Estado;
use Livewire\Component;
use Livewire\Attributes\On;

class Juego extends Component
{
    private const RONDA_EMPATE = "La ronda terminó en empate";
    private const RONDA_GANA_JUGADOR = "Ganaste la ronda";
    private const RONDA_GANA_OPONENTE = "Perdiste esta ronda";
    private const GANA_JUEGO = "¡Ganaste el juego!";
    private const PIERDE_JUEGO = "¡Perdiste el juego!";
    private const EMPATE_JUEGO = "El juego terminó en empate";
    private const JUGADOR = 'Jugador';
    private const OPONENTE = 'Oponente';
    private const RONDAS = 3;
    private const PAPEL = 0;
    private const PIEDRA = 1;
    private const TIJERA = 2;
    private const NOMBRES = [0 => 'Papel', 1 => 'Piedra', 2 => 'Tiijera'];
    public float $interval;
    public string $remainingTime = '0';
    public $ronda = 0;
    public $puntajeJugador = 0;
    public $puntajeOponente = 0;
    public $estadoActual = 'inicio';
    public $choice = -1;
    public $oponent_choice = -1;
    public $resultadoRonda = '';
    public $jugador = '';
    private FSM $fsm;

    public function boot()
    {
        $this->fsm = new FSM($this);
        $this->fsm
            ->estadoInicial()
            ->decisión('¿Existe oponente?')
            ->siguientes([
                'buscando oponente',
                'oponente encontrado'
            ])
            ->estado('buscando oponente')->setDuración(10000)
            ->siguiente('oponente encontrado')->setDuración(2000)
            ->siguiente('iterar ronda')->startIteration($this->ronda, self::RONDAS)
            ->siguiente('mostrar número ronda')->setDuración(2000)
            ->siguiente('pedir jugada')->waitFor(fn() => $this->checkChoiceMade())
            ->siguiente('calcular')->alEntrar(fn() => $this->calcular())
            ->siguiente('mostrar resultado ronda')->setDuración(4000)
            ->siguiente('fin iteración ronda')->endIteration('iterar ronda')
            ->siguiente('mostrar resultado juego')->setDuración(4000)
            ->fin();
        $this->estadoActual = session()->get('estadoActual', 'inicio');
        $this->remainingTime = session()->get('remainingTime', 0);
        $this->fsm->setEstadoActual($this->estadoActual, $this->remainingTime);
    }

    public function mount()
    {
        $this->interval = $this->fsm::UPDATE_INTERVAL;
        $this->jugador = auth()->user()->name;
    }

    public function clear()
    {
        $this->fsm->setEstadoActual('inicio', 0);
        $this->estadoActual = 'inicio';
        $this->remainingTime = 0;
        $this->ronda = 1;
        session()->put('estadoActual', 'inicio');
        session()->put('remainingTime', 0);
    }

    #[On('choice-made')]
    public function updateState()
    {
        $estado = $this->fsm->actualizar($this->getDeltaTime());
        $this->estadoActual = $estado->getNombre();
        $this->remainingTime = $this->remainingSeconds($estado);
        $this->registerTime();
        session()->put('estadoActual', $estado->getNombre());
        session()->put('remainingTime', $estado->getRestante());
    }

    public function calcular()
    {
        // 0 = papel, 1 = piedra, 2 = tijera
        $mensaje = '';
        $this->oponent_choice = rand(0, 2);
        if ($this->oponent_choice == $this->choice) {
            $mensaje = self::RONDA_EMPATE;
        } elseif ($this->oponent_choice == self::PAPEL && $this->choice == self::PIEDRA) {
            $mensaje = self::RONDA_GANA_OPONENTE;
        } elseif ($this->oponent_choice == self::PIEDRA && $this->choice == self::TIJERA) {
            $mensaje = self::RONDA_GANA_OPONENTE;
        } elseif ($this->oponent_choice == self::TIJERA && $this->choice == self::PAPEL) {
            $mensaje = self::RONDA_GANA_OPONENTE;
        } else {
            $mensaje = self::RONDA_GANA_JUGADOR;
        }
        $mensaje .= ' ' . self::NOMBRES[$this->choice] . ' vs ' . self::NOMBRES[$this->oponent_choice];
        $this->resultadoRonda = $mensaje;
        $this->choice = -1;
    }

    public function incrementarRonda()
    {
        $this->ronda++;
    }

    private function remainingSeconds(Estado $estado): int
    {
        return ceil($estado->getRestante() / 1000);
    }

    public function play(int $choice)
    {
        $this->choice = $choice;
        $this->dispatch('choice-made');
    }

    public function checkChoiceMade()
    {
        return $this->choice !== -1;
    }

    private function getDeltaTime(): float
    {
        $currentTime = floor(microtime(true) * 1000);
        $lastTime = session()->get('time');
        return ($currentTime - $lastTime);
    }

    public function registerTime()
    {
        session()->put('time', floor(microtime(true) * 1000));
    }

    public function log(string $message, string $level = 'info')
    {
        $this->dispatch('log', [
            'obj' => $message,
            'level' => $level //warn, error, debug, info, etc...
        ]);
    }

    public function render()
    {
        return view('livewire.juego');
    }
}
