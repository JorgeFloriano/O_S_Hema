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
    public $description;
    public $subdescription;

    public function __construct($objs, $obj, $des, $val='', $place='', $ind='', $subdes='')
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
        $this->description = $des;
        $this->subdescription = $subdes;
    }
    public function render(): View|Closure|string
    {
        return view('components.unlabeled-dlist');
    }
}
