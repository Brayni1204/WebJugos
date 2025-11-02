<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $title;
    public $message;

    public function __construct($type = 'success', $title = 'Mensaje', $message = 'Operación realizada correctamente')
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
