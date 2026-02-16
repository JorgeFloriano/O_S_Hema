<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CodeIndexHeader extends Component
{
    public $opt;
    public $msg;
    public $text;
    public $title;
    public $object;
    public $objects;
    public $permission;
    /**
     * Create a new component instance.
     */
    public function __construct($opt, $msg, $text, $title, $object, $objects = null, $permission = 'codes')
    {
        $this->opt = $opt;
        $this->msg = $msg;
        $this->text = $text;
        $this->title = $title;
        $this->object = $object;
        $this->objects = $objects;
        $this->permission = $permission;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.code-index-header');
    }
}
