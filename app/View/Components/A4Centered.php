<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class A4Centered extends Component
{
    public $title;
    public $text;
    public $title_size;
    public $orders_count;
    public function __construct($tit, $tex='', $tit_size=26, $nords='')
    {
        $this->title = $tit;
        $this->text = $tex;
        $this->title_size = $tit_size;
        $this->orders_count = $nords;
    }

    public function render(): View|Closure|string
    {
        return view('components.a4-centered');
    }
}
