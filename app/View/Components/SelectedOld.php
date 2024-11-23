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
    public$description;
    public function __construct($tab, $nam, $nom, $des)
    {
        $this->table = $tab;
        $this->name = $nam;
        $this->nome = $nom;
        $this->description = $des;
    }

    public function render(): View|Closure|string
    {
        return view('components.selected-old');
    }
}
