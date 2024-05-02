<?php

namespace App\FSM;

use Livewire\Component;

class StateMachine extends Component
{
    const SAVED_TIME_NAME = 'time';
    const SAVED_CURRENT_STATE_NAME = 'current_state_name';
    const SAVED_REMAINING_TIME_NAME = 'current_state_remaining_time';
    const INITIAL_STATE_NAME = 'inicio';
    const FINAL_STATE_NAME = 'fin';
    const MAXIMUM_DELTA_TIME = 10000; // ms
    const UPDATE_INTERVAL = 1000; // ms
    private $states = [];
    private State $current_state;
    public string $current_state_name = self::INITIAL_STATE_NAME;
    public string $current_state_remaining_time = '0';
    public string $ui_state = '';

    public function boot()
    {
        $this->initialState();
        $this->finalState();
        $this->current_state_name = session()->get(
            self::SAVED_CURRENT_STATE_NAME,
            self::INITIAL_STATE_NAME
        );
        $this->current_state_remaining_time = session()->get(self::SAVED_REMAINING_TIME_NAME, 0);
        $this->setCurrentState($this->current_state_name, $this->current_state_remaining_time);
    }

    public function mount()
    {
        $this->interval = self::UPDATE_INTERVAL;
    }

    public function clear()
    {
        $this->setCurrentState(self::INITIAL_STATE_NAME, 0);
        $this->current_state_name = self::INITIAL_STATE_NAME;
        $this->current_state_remaining_time = 0;
        session()->put(self::SAVED_CURRENT_STATE_NAME, $this->current_state_name);
        session()->put(self::SAVED_REMAINING_TIME_NAME, $this->current_state_remaining_time);
    }

    public function updateState()
    {
        $estado = $this->update($this->getDeltaTime());
        if ($estado->isVisible()) {
            $this->ui_state = $estado->getName();
        }
        $this->current_state_name = $estado->getName();
        $this->current_state_remaining_time = $estado->getRemainingSeconds();
        $this->registerTime();
        session()->put(self::SAVED_CURRENT_STATE_NAME, $estado->getName());
        session()->put(self::SAVED_REMAINING_TIME_NAME, $estado->getRemaining());
    }

    public function setCurrentState(string $nombre, float $restante): void
    {
        $this->current_state = $this->state($nombre);
        $this->current_state->setRemaining($restante);
    }

    public function initialState(): State
    {
        $estado = $this->state(self::INITIAL_STATE_NAME)->setAsInitial();
        return $estado;
    }

    public function finalState(): State
    {
        $fin = $this->state(self::FINAL_STATE_NAME)->setAsFinal();
        return $fin;
    }

    public function state(string $name): State
    {
        if (array_key_exists($name, $this->states)) {
            return $this->states[$name];
        }
        $state = new State($this, $name);
        $this->states[$name] = $state;
        return $state;
    }

    public function update(float $deltaTime): State
    {
        // Si el tiempo es menor o igual a 1 no se actualiza el estado. Esto es para evitar
        // que se actualice el estado en cada renderizado de Livewire. Lo cual puede suceder
        // si el intervalo es muy corto en relación a la velocidad de la red o de las capacidades
        // del servidor o del cliente.
        if ($deltaTime <= 1) {
            return $this->current_state;
        }
        if ($deltaTime > self::MAXIMUM_DELTA_TIME) {
            return $this->current_state;
        }
        $estado = $this->current_state;
        // $this->log('$deltaTime = ' . $deltaTime . ' ms' . ' $estado = ' . $estado->getNombre());
        while ($deltaTime > self::UPDATE_INTERVAL) {
            $estado = $this->getNextState($estado, self::UPDATE_INTERVAL);
            $deltaTime -= self::UPDATE_INTERVAL;
        }
        $estado = $this->getNextState($estado, $deltaTime);
        $this->current_state = $estado;
        return $this->current_state;
    }

    private function getNextState(State $estado, float $deltaTime): State
    {
        $nuevoEstado = $estado->update($deltaTime);
        if ($nuevoEstado !== $estado) {
            $this->log("{$estado->getName()} -> {$nuevoEstado->getName()}");
            $estado->exit();
            $nuevoEstado->enter();
        }
        return $nuevoEstado;
    }

    private function getDeltaTime(): float
    {
        $currentTime = floor(microtime(true) * 1000);
        $lastTime = session()->get(self::SAVED_TIME_NAME);
        return ($currentTime - $lastTime);
    }

    public function registerTime()
    {
        session()->put(self::SAVED_TIME_NAME, floor(microtime(true) * 1000));
    }

    public function log(string $message, string $level = 'info')
    {
        $this->dispatch('log', [
            'obj' => $message,
            'level' => $level //warn, error, debug, info, etc...
        ]);
    }
}
