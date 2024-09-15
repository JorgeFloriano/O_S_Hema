<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Client;
use App\Models\Note;
use App\Models\Order;
use App\Models\Tec;
use App\Models\User;
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
        $orders = $this->os->select('id', 'client_id', 'tec_id','req_date')->get();

        return view('orders_list' , ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::select('id', 'name')->get();

        $tecs = Tec::all();

        return view('order_create', [
            'clients' => $clients,
            'tecs' => $tecs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'tec_id' => $request->user_id,
            'adm_id' => auth()->user()->id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);
        if ($created) {
            return redirect()->route('orders.index')->with('message', 'Ordem de serviço criada com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro ao criar ordem de serviço.');
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

        $tecs = Tec::all();

        $adm = Adm::find($order->adm_id);
            
        return view('order_edit', [
            'order' => $order,
            'clients' => $clients,
            'tecs' => $tecs,
            'adm' => $adm
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id']));

        $os = Order::find($id);
        $os->adm_id = auth()->user()->id;
        $updated_adm = $os->save();

        if ($updated && $updated_adm) {
            return redirect()->back()->with('message', 'Ordem de serviço atualizada com sucesso.');
        }
        return redirect()->back()->with('message', 'Erro ao atualizar ordem de serviço.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->os->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('orders.index')->with('message', 'Ordem de serviço deletada com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro ao deletar ordem de serviço.');
    }
}
