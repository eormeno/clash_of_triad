<?php

namespace App\Livewire;

use App\FSM\FSM;
use Livewire\Component;

class Juego extends Component
{
    public string $remainingTime = '0';

    public $estadoActual = 'inicio';

    public $jugador = '';

    public $choice = -1;

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
    }

    public function mount()
    {
        $this->jugador = auth()->user()->name;
    }

    public function updateState()
    {
        $estado = $this->fsm->actualizar();
        $this->estadoActual = $estado->getNombre();
        $this->remainingTime = number_format($estado->getRestante(), 0);
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
