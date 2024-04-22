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
            $this->siguientes[] = $this->fsm->estado($nombre);
        }
        return $this;
    }

    public function setDuración(float $duración): Estado
    {
        $this->duración = $duración;
        return $this;
    }

    public function getDuración(): float
    {
        return $this->duración;
    }

    public function setAsInteractive(): Estado
    {
        $this->esInteractivo = true;
        return $this;
    }

    public function setAsInitial(): Estado
    {
        $this->esInicio = true;
        return $this;
    }

    public function setAsFinal(): Estado
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

    public function setRestante(float $restante): Estado
    {
        $this->restante = $restante;
        return $this;
    }

    public function esFinal(): bool
    {
        return $this->esFin;
    }

    public function esInicial(): bool
    {
        return $this->esInicio;
    }

    public function esInteractivo(): bool
    {
        return $this->esInteractivo;
    }

    public function esDecisión(): bool
    {
        return $this->esDecisión;
    }

    public function esPseudoEstado(): bool
    {
        return $this->esInicio || $this->esFin || $this->esDecisión;
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
        if ($this->esPseudoEstado()) {
            $this->restante = 0;
            $this->duración = 0;
            return;
        }
        $this->restante = $this->duración;
        //$this->fsm->log('Entrando a: ' . $this->nombre);
        if ($this->alEntrar) {
            call_user_func($this->alEntrar);
        }
    }

    public function actualizar(float $deltaTime): Estado
    {
        if ($this->esFin) {
            return $this;
        }
        if (count($this->siguientes) === 0) {
            throw new \Exception('El estado "' . $this->nombre . '" no tiene estados siguientes.');
        }
        if ($this->esInteractivo) {
            return $this;
        }
        if ($this->esInicio) {
            //$this->fsm->log('Inicio: ' . $this->nombre . ' -> ' . $this->siguientes[0]->getNombre());
            return $this->siguientes[0];
        }
        if ($this->esDecisión) {
            //$this->fsm->log('Decisión: ' . $this->nombre . ' -> ' . $this->siguientes[0]->getNombre());
            return $this->siguientes[0];
            /*             if (!$this->durante) {
                            throw new \Exception('La decisión "' . $this->nombre . '" requiere un método para su lógica.');
                        }

                        if (count($this->siguientes) < 2) {
                            throw new \Exception('La decisión "' . $this->nombre . '" requiere al menos dos posibles estados siguientes.');
                        }

                        return call_user_func($this->durante, $deltaTime); */
        }
        if ($this->duración > 0) {
            //$this->fsm->log('Duración: ' . $this->nombre . ' -> ' . $this->duración . ' -> ' . $deltaTime);
            $this->restante -= $deltaTime;
            if ($this->restante <= 0) {
                return $this->siguientes[0];
            }
        }
        return $this;
    }

    public function salir(): void
    {
        if ($this->esPseudoEstado()) {
            return;
        }
        if ($this->alSalir) {
            call_user_func($this->alSalir);
        }
    }
}
