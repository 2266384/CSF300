<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;

class APIToken extends Component
{

    public $representative;

    public $state;
    public $token;



    public function mount() {

    }


    public function createToken() {

        $this->token = Str::random(40);
    }


    public function render()
    {
        return view('livewire.a-p-i-token');
    }
}
