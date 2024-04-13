<?php

namespace App\Livewire;

use Livewire\Component;

class Juego extends Component
{

    public $count = 1;

    public function rock()
    {
        $this->count++;
    }

    public function paper()
    {
        $this->count--;
    }

    public function scissors()
    {
        $this->count = 1;
    }

    public function render()
    {
        return view('livewire.juego');
    }
}
