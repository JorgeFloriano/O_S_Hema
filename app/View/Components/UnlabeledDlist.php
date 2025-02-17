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
    public $onchange;
    public $onfocus;
    public $input_hidden_name;
    public $input_hidden_value;

    public function __construct($objs, $obj, $des, $val='', $place='', $ind='', $subdes='', $onch='', $onfoc='')
    {
        $this->objects = $objs;

        // if exist index, it will be submitted in a foreach in controller, the data was required probably in a table
        if ($ind == '') {
            $this->object = $obj;
            $this->input_hidden_name = $obj;
        } else {
            $this->object = '_'.$obj;
            $this->input_hidden_name = 'ord_'.$ind;
            $this->value = '0';
        }
        $this->object = $obj;

        // ' - []' this is id mask for object, if dont exist, the value will be empty
        if ($val == ' - []') {
            $this->value = '';
        } else {
            if ($val == '') {
                $this->value = '0';
            } else {
                $this->value = $val;
                // ' - []' this is id mask for object, if dont exist, the value will be empty
            if ($val == ' - []') {
                $this->value = '';
                } else {
                    // Use preg_match to find the last number between []
                    if (preg_match('/\[(\d+)\]([^\[\]]*)$/', $val, $matches)) {
                        $last_number = $matches[1]; // The number inside the last []
                        $this->input_hidden_value =  $last_number;
                    } 
                }
            }
        }

        $this->placeholder = $place;
        $this->index = $ind;
        $this->description = $des;
        $this->subdescription = $subdes;

        // Already exist an onchange function, to add more, separate with a comma " ,"
        if ($onch != '') {
            $this->onchange = ' ,'.$onch; 
        }
        $this->onfocus = $onfoc;
    }

    public function render(): View|Closure|string
    {
        return view('components.unlabeled-dlist');
    }
}
