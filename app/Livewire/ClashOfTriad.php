<?php

namespace App\Livewire;

use App\FSM\StateMachine;
use Livewire\Attributes\On;

class ClashOfTriad extends StateMachine
{
    private const RONDAS = 3;
    private const EMPATA = 0;
    private const PIERDE = 1;
    private const GANA = 2;
    private const NINGUNO = -1;
    private const PAPEL = 0;
    private const PIEDRA = 1;
    private const TIJERA = 2;
    public int $ronda = 0;
    public int $puntajeJugador = 0;
    public int $puntajeOponente = 0;
    public int $juego_propio = self::NINGUNO;
    public int $juego_oponente = self::NINGUNO;
    public $resultadoRonda = '';
    public string $resultadoJuego = '';
    public $jugador = '';

    public function boot()
    {
        $this->initialState()
            ->decision('¿Existe oponente?')
            ->following([
                'buscando oponente',
                'oponente encontrado'
            ])
            ->state('buscando oponente')->setSeconds(10)
            ->next('oponente encontrado')->setSeconds(2)
            ->next('iterar ronda')->startIteration($this->ronda, self::RONDAS)
            ->__->next('mostrar número ronda')->setSeconds(2)
            ->__->next('pedir jugada')->waitFor(fn() => $this->esperaJugada())
            ->__->next('calcular')->onEntering(fn() => $this->calcularGanador())
            ->__->next('mostrar resultado ronda')->setSeconds(2)
            ->next('fin iteración ronda')->endIteration('iterar ronda')
            ->next('mostrar resultado juego')->onEntering(fn() => $this->finalJuego())->setSeconds(4)
            ->finalState();
        $this->ronda = session()->get('ronda', 0);
        $this->puntajeJugador = session()->get('puntajeJugador', 0);
        $this->puntajeOponente = session()->get('puntajeOponente', 0);
        parent::boot();
    }

    public function mount()
    {
        parent::mount();
        $this->jugador = auth()->user()->name;
    }

    public function clear()
    {
        parent::clear();
        $this->ronda = 0;
        $this->puntajeJugador = 0;
        $this->puntajeOponente = 0;
        session()->put('ronda', 0);
        session()->put('puntajeJugador', 0);
        session()->put('puntajeOponente', 0);
    }

    #[On('choice-made')]
    public function updateState()
    {
        parent::updateState();
        session()->put('ronda', $this->ronda);
        session()->put('puntajeJugador', $this->puntajeJugador);
        session()->put('puntajeOponente', $this->puntajeOponente);
    }

    public function calcularGanador()
    {
        $this->juego_oponente = $this->juegoOponente();
        $resultadoRonda = $this->calcularResultadoPorAngulo(
            $this->juego_propio,
            $this->juego_oponente
        );
        $this->calcularPuntaje($resultadoRonda);
        $this->resultadoRonda = $this->obtenerMensajeRonda(
            $resultadoRonda,
            $this->juego_propio,
            $this->juego_oponente
        );
        $this->juego_propio = self::NINGUNO;
    }

    public function calcularResultadoPorAngulo($opciónJugador, $opciónOponente)
    {
        $diferencia = $opciónJugador * 120 - $opciónOponente * 120;
        if ($diferencia < 0) {
            $diferencia += 360;
        } elseif ($diferencia == 0) {
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

    public function juegoPropio(int $miElección)
    {
        $this->juego_propio = $miElección;
        $this->dispatch('choice-made');
    }

    public function esperaJugada()
    {
        return $this->juego_propio !== -1;
    }

    public function render()
    {
        return view('livewire.clash-of-triad');
    }
}
