<?php

namespace App\Livewire;

use App\FSM\FSM;
use App\FSM\Estado;
use Livewire\Component;

class Juego extends Component
{
    public float $interval;
    public string $remainingTime = '0';
    public $estadoActual = 'inicio';
    public $jugador = '';
    public $choice = -1;
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
            ->estado('buscando oponente')->setDuración(15000)
            ->siguiente('oponente encontrado')->setDuración(4000)
            ->siguiente('mostrar número ronda')->setDuración(3000)
            ->siguiente('pedir jugada')->setAsInteractive()
            ->siguiente('calcular')
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
        session()->put('estadoActual', 'inicio');
        session()->put('remainingTime', 0);
    }

    public function updateState()
    {
        $estado = $this->fsm->actualizar($this->getDeltaTime());
        $this->estadoActual = $estado->getNombre();
        $this->remainingTime = $this->remainingSeconds($estado);
        $this->registerTime();
        session()->put('estadoActual', $estado->getNombre());
        session()->put('remainingTime', $estado->getRestante());
    }

    private function remainingSeconds(Estado $estado): int
    {
        return ceil($estado->getRestante() / 1000);
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
