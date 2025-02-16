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
    public $onchange;
    public $required;
    public $onfocus;
    public function __construct($objs, $obj, $tit, $val='',$des, $list='', $req='')
    {
        $this->objects = $objs;
        $this->object = $obj;
        $this->title = $tit;
        $this->value = $val;
        $this->description = $des;

        if ($list == "true") {
            $this->onchange = ", manageList('material')";
            $this->onfocus = "this.value = ''";
        } else {
            $this->onchange = '';
            $this->onfocus = '';
        }

        $this->required = $req;
    }

    public function render(): View|Closure|string
    {
        return view('components.datalist');
    }
}
