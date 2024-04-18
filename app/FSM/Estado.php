<?php

namespace App\FSM;

class Estado
{
    private FSM $fsm;
    private string $nombre;
    private float $duración = 0;
    private bool $esInicio = false;
    private bool $esFin = false;
    private bool $esDecisión = false;
    private bool $esInteractivo = false;
    private array $siguientes = [];
    private float $restante = 0;
    private $alEntrar = null;
    private $durante = null;
    private $alSalir = null;

    public function __construct(FSM $fsm, string $nombre)
    {
        $this->fsm = $fsm;
        $this->nombre = $nombre;
    }

    public function siguiente(string $nombre): Estado
    {
        $siguiente = $this->fsm->estado($nombre);
        $this->siguientes[] = $siguiente;
        return $siguiente;
    }

    public function decisión(string $nombre): Estado
    {
        $decisión = $this->fsm->estado($nombre);
        $decisión->esDecisión = true;
        $this->siguientes[] = $decisión;
        return $decisión;
    }

    public function fin(): void
    {
        $this->siguiente('fin');
    }

    public function siguientes(array $siguientes): Estado
    {
        foreach ($siguientes as $nombre) {
            $this->siguiente($nombre);
        }
        return $this;
    }

    public function duración(float $duración): Estado
    {
        $this->duración = $duración;
        return $this;
    }

    public function interactivo(): Estado
    {
        $this->esInteractivo = true;
        return $this;
    }

    public function esInicial(): Estado
    {
        $this->esInicio = true;
        return $this;
    }

    public function esFinal(): Estado
    {
        $this->esFin = true;
        return $this;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getRestante(): float
    {
        return $this->restante;
    }

    public function alEntrar(callable $alEntrar): Estado
    {
        $this->alEntrar = $alEntrar;
        return $this;
    }

    public function alSalir(callable $alSalir): Estado
    {
        $this->alSalir = $alSalir;
        return $this;
    }

    public function durante(callable $durante): Estado
    {
        $this->durante = $durante;
        return $this;
    }

    public function estado(string $nombre): Estado
    {
        return $this->fsm->estado($nombre);
    }

    public function entrar(): void
    {
        $this->restante = $this->duración;
        if ($this->alEntrar) {
            call_user_func($this->alEntrar);
        }
    }

    public function actualizar(float $deltaTime): ?Estado
    {
        if ($this->esFin) {
            return null;
        }

        if ($this->esInicio) {
            return $this->siguientes[0];
        }

        if ($this->esDecisión && !$this->durante) {
            throw new \Exception('La decisión "' . $this->nombre . '" requiere un método para su lógica.');
        }

        if ($this->durante) {
            return call_user_func($this->durante, $deltaTime);
        }

        if ($this->duración > 0) {
            $this->remainingTime -= $deltaTime;
            if ($this->restante <= 0) {
                return $this->siguientes[0];
            }
        }
        return $this;
    }

    public function salir(): void
    {
        if ($this->alSalir) {
            call_user_func($this->alSalir);
        }
    }
}
