<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Note;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public readonly Order $os;

    public function __construct()
    {
        $this->os = new Order();
    }

    public function index()
    {
        $orders = $this->os->select('id', 'client_id','req_date')->get();

        return view('orders_list' , ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::select('id', 'name')->get();

        return view('order_create', ['clients' => $clients]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);
        if ($created) {
            return redirect()->route('orders.index')->with('message', 'Cadastro realizado com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro no cadastro.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return view('order_delete', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $clients = Client::select('id', 'name')->get();

        return view('order_edit', [
            'order' => $order,
            'clients' => $clients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
