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
    public $type;
    public $input_hidden_value;
    public function __construct($objs, $obj, $tit, $val='',$des, $type='', $req='')
    {
        $this->objects = $objs;
        $this->object = $obj;
        $this->title = $tit;
        $this->value = $val;
        $this->description = $des;
        $this->type = $type;

        if (preg_match('/\[(\d+)\]([^\[\]]*)$/', $val, $matches)) {
            $last_number = $matches[1]; // The number inside the last []
            $this->input_hidden_value =  $last_number;
        } 

        if ($type == "list") {
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
