<?php

namespace App\FSM;

class State
{
    private StateMachine $fsm;
    private string $name;
    private float $duration_ms = 0;
    private bool $isStart = false;
    private bool $isEnd = false;
    private bool $isDecision = false;
    private bool $isInteractive = false;
    private bool $isStartIteration = false;
    private bool $isEndIteration = false;
    private State $iterationStart;
    private State $iterationEnd;
    private int $from = 1;
    private int $to = -1;
    private $variable = null;
    private array $nextStates = [];
    private float $remaining = 0;
    private $onEntering = null;
    private $durante = null;
    private $onExiting = null;

    protected $_data = array(
        '__' => null
    );

    public function __construct(StateMachine $fsm, string $name)
    {
        $this->fsm = $fsm;
        $this->name = $name;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this;
        }
        return null;
    }

    public function isVisible(): bool
    {
        return $this->isEnd || $this->isInteractive || $this->duration_ms > 0;
    }

    public function next(string $name): State
    {
        $next = $this->fsm->state($name);
        $this->nextStates[] = $next;
        return $next;
    }

    public function decision(string $nombre): State
    {
        $decisión = $this->fsm->state($nombre);
        $decisión->isDecision = true;
        $this->nextStates[] = $decisión;
        return $decisión;
    }

    public function finalState(): void
    {
        $this->next(StateMachine::FINAL_STATE_NAME);
    }

    public function following(array $following): State
    {
        foreach ($following as $nombre) {
            $this->nextStates[] = $this->fsm->state($nombre);
        }
        return $this;
    }

    public function setSeconds(float $duración): State
    {
        $this->duration_ms = $duración * 1000;
        return $this;
    }

    public function getSeconds(): float
    {
        return $this->duration_ms / 1000;
    }

    public function waitFor(callable $durante): State
    {
        $this->isInteractive = true;
        $this->durante = $durante;
        return $this;
    }

    public function startIteration(&$variable, int $to, int $from = 1): State {
        $this->isStartIteration = true;
        $this->variable = &$variable;
        $this->to = $to;
        $this->from = $from;
        return $this;
    }

    public function endIteration(string $name): State {
        $this->isEndIteration = true;
        $itarationState = $this->fsm->state($name);
        $itarationState->iterationEnd = $this;
        $this->iterationStart = $itarationState;
        return $this;
    }

    public function setAsInitial(): State
    {
        $this->isStart = true;
        return $this;
    }

    public function setAsFinal(): State
    {
        $this->isEnd = true;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRemaining(): float
    {
        return $this->remaining;
    }

    public function setRemaining(float $remaining): State
    {
        $this->remaining = $remaining;
        return $this;
    }

    public function getRemainingSeconds(): int
    {
        return floor($this->remaining / 1000);
    }

    public function isEnd(): bool
    {
        return $this->isEnd;
    }

    public function isInitial(): bool
    {
        return $this->isStart;
    }

    public function isInteractive(): bool
    {
        return $this->isInteractive;
    }

    public function isDecision(): bool
    {
        return $this->isDecision;
    }

    public function isPseudo(): bool
    {
        return $this->isStart || $this->isEnd || $this->isDecision;
    }

    public function onEntering(callable $onEnterMethod): State
    {
        $this->onEntering = $onEnterMethod;
        return $this;
    }

    public function onExiting(callable $onExitingMethod): State
    {
        $this->onExiting = $onExitingMethod;
        return $this;
    }

    public function durante(callable $durante): State
    {
        $this->durante = $durante;
        return $this;
    }

    public function state(string $name): State
    {
        return $this->fsm->state($name);
    }

    public function enter(): void
    {
        if ($this->isPseudo()) {
            $this->remaining = 0;
            $this->duration_ms = 0;
            return;
        }
        $this->remaining = $this->duration_ms;
        if ($this->onEntering) {
            call_user_func($this->onEntering);
        }
    }

    public function exit(): void
    {
        if ($this->isPseudo()) {
            return;
        }
        if ($this->onExiting) {
            call_user_func($this->onExiting);
        }
    }

    public function update(float $deltaTime): State
    {
        if ($this->isEnd) {
            return $this;
        }
        if (count($this->nextStates) === 0) {
            throw new \Exception('The state "' . $this->name . '" does not have next states.');
        }
        if ($this->isInteractive) {
            if ($this->durante) {
                if (call_user_func($this->durante)) {
                    return $this->nextStates[0];
                }
            }
            return $this;
        }
        if ($this->isStart) {
            return $this->nextStates[0];
        }
        if ($this->isDecision) {
            //$this->fsm->log('Decisión: ' . $this->nombre . ' -> ' . $this->siguientes[0]->getNombre());
            return $this->nextStates[0];
            /*             if (!$this->durante) {
                            throw new \Exception('La decisión "' . $this->nombre . '" requiere un método para su lógica.');
                        }

                        if (count($this->siguientes) < 2) {
                            throw new \Exception('La decisión "' . $this->nombre . '" requiere al menos dos posibles estados siguientes.');
                        }

                        return call_user_func($this->durante, $deltaTime); */
        }

        if ($this->isStartIteration) {
            if ($this->variable < $this->to) {
                $this->variable++;
                return $this->nextStates[0];
            }
            return $this->iterationEnd->nextStates[0];
        }

        if ($this->isEndIteration) {
            return $this->iterationStart;
        }

        if ($this->duration_ms >= 0) {
            // $this->fsm->log('"' . $this->name . '" -> ' . $this->getRemainingSeconds() . ' -> ' . $deltaTime);
            $this->remaining -= $deltaTime;
            if ($this->remaining <= 0) {
                return $this->nextStates[0];
            }
        }
        return $this;
    }
}
