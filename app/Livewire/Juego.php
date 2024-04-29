<?php

namespace App\Livewire;

use App\FSM\FSM;
use App\FSM\Estado;
use Livewire\Component;
use Livewire\Attributes\On;

class Juego extends Component
{
    private const RONDAS = 3;
    private const EMPATA = 0;
    private const PIERDE = 1;
    private const GANA = 2;
    private const NINGUNO = -1;
    private const PAPEL = 0;
    private const PIEDRA = 1;
    private const TIJERA = 2;
    public float $interval;
    public string $remainingTime = '0';
    public int $ronda = 0;
    public int $puntajeJugador = 0;
    public int $puntajeOponente = 0;
    public $estadoActual = 'inicio';
    public $estadoUI;
    public int $choice = self::NINGUNO;
    public int $oponent_choice = self::NINGUNO;
    public $resultadoRonda = '';
    public string $resultadoJuego = '';
    public $jugador = '';
    private FSM $fsm;

    public function boot()
    {
        $this->fsm = new FSM($this);
        $this->fsm
            ->inicio()
            ->decisión('¿Existe oponente?')
            ->siguientes([
                'buscando oponente',
                'oponente encontrado'
            ])
            ->estado('buscando oponente')->setDuración(10000)
            ->siguiente('oponente encontrado')->setDuración(2000)
            ->siguiente('iterar ronda')->startIteration($this->ronda, self::RONDAS)
            ->__->siguiente('mostrar número ronda')->setDuración(2000)
            ->__->siguiente('pedir jugada')->waitFor(fn() => $this->checkChoiceMade())
            ->__->siguiente('calcular')->alEntrar(fn() => $this->calcular())
            ->__->siguiente('mostrar resultado ronda')->setDuración(4000)
            ->siguiente('fin iteración ronda')->endIteration('iterar ronda')
            ->siguiente('mostrar resultado juego')->alEntrar(fn() => $this->finalJuego())->setDuración(4000)
            ->fin();
        $this->estadoActual = session()->get('estadoActual', 'inicio');
        $this->remainingTime = session()->get('remainingTime', 0);
        $this->ronda = session()->get('ronda', 0);
        $this->puntajeJugador = session()->get('puntajeJugador', 0);
        $this->puntajeOponente = session()->get('puntajeOponente', 0);
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
        $this->ronda = 0;
        $this->puntajeJugador = 0;
        $this->puntajeOponente = 0;
        session()->put('estadoActual', 'inicio');
        session()->put('remainingTime', 0);
        session()->put('ronda', 0);
        session()->put('puntajeJugador', 0);
        session()->put('puntajeOponente', 0);
    }

    #[On('choice-made')]
    public function updateState()
    {
        $estado = $this->fsm->actualizar($this->getDeltaTime());
        if ($estado->isVisible()) {
            $this->estadoUI = $estado->getNombre();
        }
        $this->estadoActual = $estado->getNombre();
        $this->remainingTime = $this->remainingSeconds($estado);
        $this->registerTime();
        session()->put('estadoActual', $estado->getNombre());
        session()->put('remainingTime', $estado->getRestante());
        session()->put('ronda', $this->ronda);
        session()->put('puntajeJugador', $this->puntajeJugador);
        session()->put('puntajeOponente', $this->puntajeOponente);
    }

    public function calcular()
    {
        $this->oponent_choice = $this->juegoOponente();
        $resultadoRonda = $this->calcularResultadoPorAngulo($this->choice, $this->oponent_choice);
        $this->calcularPuntaje($resultadoRonda);
        $this->resultadoRonda = $this->obtenerMensajeRonda(
            $resultadoRonda,
            $this->choice,
            $this->oponent_choice
        );
        $this->choice = self::NINGUNO;
    }

    public function calcularResultadoPorAngulo($opciónJugador, $opciónOponente)
    {
        $diferencia = $opciónJugador * 120 - $opciónOponente * 120;
        if ($diferencia < 0) {
            $diferencia += 360;
        } elseif ($diferencia ==0) {
            return self::EMPATA;
        }
        // si es par gana el jugador
        return ($diferencia / 120) % 2 == 0 ? self::GANA : self::PIERDE;
    }

    private function juegoOponente(): int
    {
        return rand(0, 2);
    }

    private function calcularPuntaje(int $resultadoRonda)
    {
        if ($resultadoRonda == self::EMPATA) {
            $this->puntajeJugador++;
            $this->puntajeOponente++;
        } elseif ($resultadoRonda == self::GANA) {
            $this->puntajeJugador += 2;
        } else {
            $this->puntajeOponente += 2;
        }
    }

    private function finalJuego()
    {
        if ($this->puntajeJugador == $this->puntajeOponente) {
            $this->resultadoJuego = __('juego.game_result.0');
        } elseif ($this->puntajeJugador > $this->puntajeOponente) {
            $this->resultadoJuego = __('juego.game_result.2');
        } else {
            $this->resultadoJuego = __('juego.game_result.1');
        }
    }

    private function obtenerMensajeRonda(int $resultadoRonda, int $my_choice, $oponent_choice): string
    {
        return __('juego.round_message', [
            'round' => $this->ronda,
            'result' => __('juego.round_result.' . $resultadoRonda),
            'jugador' => __('juego.choice.' . $my_choice),
            'oponente' => __('juego.choice.' . $oponent_choice)
        ]);
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
