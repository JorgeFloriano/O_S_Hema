<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

        if ($request->client_id == '0') {
            return redirect()->route('orders.create')->with('message', 'Selecione um cliente para prosseguir.');
        }

        //If there is no contact name, then use the name of the client that was selected
        $cont_name_client = $request->req_name;
        if (!$request->req_name) {
            $cont_name_client = Client::find($request->client_id)->contact;
        }

        //Create new order
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'sector' => $request->sector,
            'req_name' => $cont_name_client,
            'tec_id' => $request->tec_id,
            'user_id' => auth()->user()->id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);
        
        if ($created) {
            if ((isset(auth()->user()->tec))) {
                return redirect()->route('notes.index')->with('message', 'Ordem de serviço criada com sucesso.');
            }
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
        $clients = Client::select(['id', 'name'])->get();

        $tecs = Tec::all();

        $user = User::select('name')->find($order->user_id);
            
        return view('order_edit', [
            'order' => $order,
            'clients' => $clients,
            'tecs' => $tecs,
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        if ($request->client_id == '0') {
            return redirect()->back()->with('message', 'Selecione um cliente para prosseguir.');
        }

        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id']));

        $os = Order::find($id);
        $os->user_id = auth()->user()->id;
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

    public function finish(Order $order): RedirectResponse
    {
        $order->finished = true;
        $updated = $order->save();

        if ($updated) {
            return redirect()->back()->with('message', 'Ordem de serviço finalizada com sucesso.');
        }

        return redirect()->back()->with('message', 'Erro ao finalizar Ordem de serviço.');
    }
    public function show_pdf(Order $order)
    {
        return view('order_pdf', ['order' => $order]);
    }
}
