<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UnlabeledDlist extends Component
{
    public $objects;
    public $object;
    public $value;
    public $placeholder;
    public $index;

    public function __construct($objs, $obj, $val='', $place='', $ind='')
    {
        $this->objects = $objs;
        $this->object = $obj;

        if ($val == ' - []') {
            $this->value = '';
        } else {
            $this->value = $val;
        }

        $this->placeholder = $place;
        $this->index = $ind;
    }
    public function render(): View|Closure|string
    {
        return view('components.unlabeled-dlist');
    }
}
