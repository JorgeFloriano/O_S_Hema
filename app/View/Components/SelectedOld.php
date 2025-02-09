<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectedOld extends Component
{
    public $table;
    public $name;
    public $nome;
    public $description;
    public $jsfunction;
    public function __construct($tab, $nam, $nom, $des, $jsfunc='')
    {
        $this->table = $tab;
        $this->name = $nam;
        $this->nome = $nom;
        $this->description = $des;
        $this->jsfunction = $jsfunc;
    }

    public function render(): View|Closure|string
    {
        return view('components.selected-old');
    }
}
