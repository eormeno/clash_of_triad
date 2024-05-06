<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class DebugBar extends Component
{
    #[Reactive]
    public string $current_state_name = '';
    #[Reactive]
    public string $current_state_remaining_time = '';
    #[Reactive]
    public $delta_time = 0;
    public string $player_name = '';

    public function mount($current_state_name, $current_state_remaining_time, $delta_time, $player_name)
    {
        $this->current_state_name = $current_state_name;
        $this->current_state_remaining_time = $current_state_remaining_time;
        $this->delta_time = $delta_time;
        $this->player_name = $player_name;
    }

    public function render()
    {
        return view('livewire.debug-bar');
    }
}
