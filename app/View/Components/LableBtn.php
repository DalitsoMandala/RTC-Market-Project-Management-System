<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LableBtn extends Component
{
    /**
     * Create a new component instance.
     */

    public $color ;
    public $icon ;
 public function __construct($color = 'btn-warning', $icon = 'bx bx-pen')
{
    $this->color = $color;
    $this->icon = $icon;
}
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lable-btn');
    }
}
