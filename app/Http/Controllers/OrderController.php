<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormOrderRequest;
use App\Models\Client;
use App\Models\NoteTec;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public readonly Order $os;
    public $m; // main administrator
    public $s; // supervisor
    public $a; // administrator
    public $t; // technician
    public $o; // technician on call
    public function __construct()
    {
        // Set a nem service order
        $this->os = new Order();

        // user is admin main or not
        if (isset(auth()->user()->adm)) {
            $this->m = auth()->user()->adm()->first()->main;
        }

        // user is technician on call or not
        if (isset(auth()->user()->tec)) {
            $this->o = auth()->user()->tec()->first()->on_call;
        }

        // user is supervisor or not
        $this->s = auth()->user()->sup()->first();

        // user is administrator or not
        $this->a = auth()->user()->adm()->first();

        // user is technician or not
        $this->t = auth()->user()->tec()->first();
    }
    public function index()
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->s && !$this->a) {return view('login');}

        $orders = $this->os->select('id', 'order_type_id','client_id', 'tec_id','req_date', 'finished')->orderBy('id', 'desc')->simplePaginate(10);   

        $tecs = Tec::all();

        session()->put('ords', $orders);

        return view('order.orders_list' , [
            'orders' => $orders,
            'tecs' => $tecs,
            'main' => $this->m ?? null,
            'sup' => $this->s ?? null,
            'adm' => $this->a ?? null
        ]);
    }

    // Show the form for creating a new order
    public function create()
    {
        // If user is not administrator or on call technician, redirect to login
        if (!$this->a && !$this->o) {return view('login');}
        
        $clients = Client::select('id', 'name')->get();

        $types = OrderType::all();
        session()->put('types_ids', $types->pluck('id')->toArray());

        $tecs = Tec::all();

        return view('order.order_create', [
            'clients' => $clients,
            'tecs' => $tecs,
            'types' => $types
        ]);
    }

    // Create a new order
    public function store(FormOrderRequest $request)
    {

        // If user is not administrator or on call technician, redirect to login
        if (!$this->a && !$this->o) {return view('login');}

        $request->validated();

        // If there is no contact name, then use the name of the client that was selected
        $cont_name_client = $request->req_name;
        if (!$request->req_name) {
            $cont_name_client = Client::find($request->client_id)->contact;
        }

        // If user that is creating the order is a technician, set the order tec_id to the tec_id itself
        $tec_id = null;
        if (isset(auth()->user()->tec)) {
            $tec_id = auth()->user()->tec->id;
        }

        //Create new order
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'order_type_id' => $request->order_type_id,
            'sector' => $request->sector,
            'req_name' => $cont_name_client,
            'user_id' => auth()->user()->id,
            'tec_id' => $tec_id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);
        
        $msg = $created ? 'Ordem de serviço criada com sucesso.' : 'Erro ao criar ordem de serviço.';
        $route = $this->o && !$this->a ? 'notes.index' : 'orders.index';     
        return redirect()->route($route)->with('message', $msg);
    }

    // Shows the form to delete the order
    public function show(Order $order)
    {
        // Only administrator can delete orders
        if (!$this->a) {
            return view('login');
        }

        return view('order.order_delete', ['order' => $order]);
    }

    // Shows the form to edit the order
    public function edit(Order $order)
    {
        // Only administrators can edit orders, supervisors can just see them
        if (!$this->a && !$this->s) {
            return view('login');
        }

        $clients = Client::select(['id', 'name'])->get();

        $tecs = Tec::all();

        $types = OrderType::all();
        session()->put('types_ids', $types->pluck('id')->toArray());

        $user = User::select('name')->find($order->user_id);

        // Remove seconds from requests time format
        $order->req_time = date_format(date_create($order->req_time), 'H:i');

        $disabled = $this->a ? '' : 'disabled';
        $title = $this->a ? '' : 'Informações do';
        
        return view('order.order_edit', [
            'order' => $order,
            'types' => $types,
            'clients' => $clients,
            'tecs' => $tecs,
            'user' => $user,
            'disabled' => $disabled,
            'title' => $title
        ]);
    }

    // Only administrators can update orders.
    public function update(FormOrderRequest $request, string $id)
    {
        if (!$this->a) {return view('login');}

        $request->validated();

        if ($request->client_id == '0') {return redirect()->back()->with('message', 'Selecione um cliente para prosseguir.');}

        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id', 'tec_id']));

        $os = Order::find($id);
        $os->user_id = auth()->user()->id;
        $updated_adm = $os->save();

        $msg = $updated && $updated_adm ? 'Ordem de serviço atualizada com sucesso.' : 'Erro ao atualizar ordem de serviço.';
        return redirect()->back()->with('message', $msg);
    }

    // Only administrators can delete orders
    public function destroy(string $id)
    {
        if (!$this->a) {return view('login');}

        $order = $this->os->find($id);
        
        foreach ($order->notes as $key => $note) {
            foreach ($note->tecs as $key => $tec) {
                $note_tec = NoteTec::where('note_id', $note->id)->where('tec_id', $tec->id)->first();
                $note_tec->delete();
            }
            $note->delete();
        }

        $deleted = $order->delete();

        $msg = $deleted ? 'Ordem de serviço deletada com sucesso.' : 'Erro ao deletar ordem de serviço.';
        return redirect()->route('orders.index')->with('message', $msg);
    }

    public function finish(Order $order)
    {
        if (!$this->t) {
            return view('login');
        }

        $order->finished = true;
        $updated = $order->save();

        $msg = $updated ? 'Ordem de serviço finalizada com sucesso.' : 'Erro ao finalizar Ordem de serviço.';
        return redirect()->back()->with('message', $msg);
    }

    // Shows the PDF for the order
    public function show_pdf(Order $order)
    {
        return view('order.order_pdf', ['order' => $order]);
    }

    // Only main administrators or supervisors can change the on call technician
    public function ord_tec_update(Request $request)
    {
        if (!$this->s && !$this->m) {return view('login');}

        $ords = session('ords');

        foreach ($ords as  $ord) {
            $ord->tec_id = $request->input('ord_'.$ord->id);
            $ord = $ord->save();
            if (!$ord) {
                return redirect()->back()->with('message', 'Erro ao selecionar técnico.'); 
            }
        }

        return redirect()->back()->with('message', 'Técnico selecionado com sucesso.');
    }
}
