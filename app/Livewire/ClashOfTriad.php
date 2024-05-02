<?php

namespace App\Livewire;

use App\FSM\StateMachine;

class ClashOfTriad extends StateMachine
{
    private const RONDAS = 3;
    private const EMPATA = 0;
    private const PIERDE = 1;
    private const GANA = 2;
    private const NINGUNO_AUN = -1;
    private const PAPEL = 0;
    private const PIEDRA = 1;
    private const TIJERA = 2;
    public int $ronda = 0;
    public int $puntaje_jugador = 0;
    public int $puntaje_oponente = 0;
    public int $juego_propio = self::NINGUNO_AUN;
    public int $juego_oponente = self::NINGUNO_AUN;
    public $resultado_ronda = '';
    public string $resultado_juego = '';
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
        $this->puntaje_jugador = session()->get('puntajeJugador', 0);
        $this->puntaje_oponente = session()->get('puntajeOponente', 0);
        parent::boot();
    }

    public function mount()
    {
        $this->jugador = auth()->user()->name;
    }

    public function clear()
    {
        parent::clear();
        $this->ronda = 0;
        $this->puntaje_jugador = 0;
        $this->puntaje_oponente = 0;
        session()->put('ronda', 0);
        session()->put('puntajeJugador', 0);
        session()->put('puntajeOponente', 0);
    }

    public function updateState()
    {
        parent::updateState();
        session()->put('ronda', $this->ronda);
        session()->put('puntajeJugador', $this->puntaje_jugador);
        session()->put('puntajeOponente', $this->puntaje_oponente);
    }

    public function calcularGanador()
    {
        $this->juego_oponente = $this->juegoOponente();
        $resultadoRonda = $this->calcularResultadoPorAngulo(
            $this->juego_propio,
            $this->juego_oponente
        );
        $this->calcularPuntaje($resultadoRonda);
        $this->resultado_ronda = $this->obtenerMensajeRonda(
            $resultadoRonda,
            $this->juego_propio,
            $this->juego_oponente
        );
        $this->juego_propio = self::NINGUNO_AUN;
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

    public function juegoPropio(int $miElección)
    {
        $this->juego_propio = $miElección;
    }

    private function juegoOponente(): int
    {
        return rand(0, 2);
    }

    private function calcularPuntaje(int $resultadoRonda)
    {
        if ($resultadoRonda == self::EMPATA) {
            $this->puntaje_jugador++;
            $this->puntaje_oponente++;
        } elseif ($resultadoRonda == self::GANA) {
            $this->puntaje_jugador += 2;
        } else {
            $this->puntaje_oponente += 2;
        }
    }

    private function finalJuego()
    {
        if ($this->puntaje_jugador == $this->puntaje_oponente) {
            $this->resultado_juego = __('juego.game_result.0');
        } elseif ($this->puntaje_jugador > $this->puntaje_oponente) {
            $this->resultado_juego = __('juego.game_result.2');
        } else {
            $this->resultado_juego = __('juego.game_result.1');
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

    public function esperaJugada()
    {
        return $this->juego_propio !== self::NINGUNO_AUN;
    }

    public function render()
    {
        return view('livewire.clash-of-triad');
    }
}
