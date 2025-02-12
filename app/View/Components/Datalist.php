<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datalist extends Component
{
    public $objects;
    public $object;
    public $title;
    public $value;
    public $description;
    public $jsfunction;
    public $required;
    public function __construct($objs, $obj, $tit, $val='',$des, $jsf='', $req='')
    {
        $this->objects = $objs;
        $this->object = $obj;
        $this->title = $tit;
        $this->value = $val;
        $this->description = $des;
        $this->jsfunction = $jsf;
        $this->required = $req;
    }

    public function render(): View|Closure|string
    {
        return view('components.datalist');
    }
}
