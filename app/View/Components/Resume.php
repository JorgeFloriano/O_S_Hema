<?php

namespace App\View\Components;

use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Resume extends Component
{
    public $order_by_clients;
    public $page;
    public function __construct($orders='', $page='')
    {
        $this->order_by_clients = $orders;
        $this->page = $page;
    }

    
    public function render(): View|Closure|string
    {
        return view('components.resume');
    }
}
