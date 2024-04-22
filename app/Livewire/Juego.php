<?php

namespace App\Livewire;

use App\FSM\FSM;
use App\FSM\Estado;
use Livewire\Component;
use Livewire\Attributes\On;

class Juego extends Component
{
    public float $interval;
    public string $remainingTime = '0';
    public $estadoActual = 'inicio';
    public $oponentChoice = -1;
    public $resultadoRonda = '';
    public $variables = [];
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
            ->siguiente('mostrar número ronda')->setDuración(2000)
            ->siguiente('pedir jugada')->waitFor("play")
            ->siguiente('calcular')->alEntrar(fn () => $this->calcular())
            ->siguiente('mostrar resultado ronda')->setDuración(2000)
            ->siguiente('incrementar ronda')
            ->decisión('¿Es fin de juego?')
            ->siguientes([
                'mostrar resultado juego',
                'mostrar número ronda'
            ])
            ->estado('mostrar resultado juego')->setDuración(4000)
            ->fin();
        $this->estadoActual = session()->get('estadoActual', 'inicio');
        $this->remainingTime = session()->get('remainingTime', 0);
        $this->variables = session()->get('variables', []);
        $this->fsm->setEstadoActual($this->estadoActual, $this->remainingTime, $this->variables);
    }

    public function mount()
    {
        $this->interval = $this->fsm::UPDATE_INTERVAL;
        $this->jugador = auth()->user()->name;
    }

    public function clear()
    {
        $this->fsm->setEstadoActual('inicio', 0, []);
        $this->estadoActual = 'inicio';
        $this->remainingTime = 0;
        session()->put('variables', []);
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
        session()->put('variables', $this->variables);
    }

    public function calcular()
    {
        // 0 = papel, 1 = piedra, 2 = tijera
        $this->oponent_choice = rand(0, 2);
        $my_choice = $this->variables['play'];
        $resultado = '';
        if ($this->oponent_choice == $my_choice) {
            $resultado = 'Empate';
        } elseif ($this->oponent_choice == 0 && $my_choice == 1) {
            $resultado = 'Gana oponente';
        } elseif ($this->oponent_choice == 1 && $my_choice == 2) {
            $resultado = 'Gana oponente';
        } elseif ($this->oponent_choice == 2 && $my_choice == 0) {
            $resultado = 'Gana oponente';
        } else {
            $resultado = 'Gana jugador';
        }
        $this->resultadoRonda = $resultado;

        session()->put('variables', $this->variables);
        $this->dispatch('choice-made');
    }

    private function remainingSeconds(Estado $estado): int
    {
        return ceil($estado->getRestante() / 1000);
    }

    public function play(int $choice)
    {
        $this->variables['play'] = $choice;
        session()->put('variables', $this->variables);
        $this->dispatch('choice-made');
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
