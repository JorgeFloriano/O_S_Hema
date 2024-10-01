<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NoteTec;
use App\Models\Order;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public readonly Order $os;
    public $m; // user is admin main or not
    public $s; // user is supervisor or not
    public $a; // user is admin or not
    public $t; // user is tec or not
    public $o; // user is tec on call or not
    public function __construct()
    {
        $this->os = new Order();
        if (isset(auth()->user()->adm)) {
            $this->m = auth()->user()->adm()->first()->main;
        }
        if (isset(auth()->user()->tec)) {
            $this->o = auth()->user()->tec()->first()->on_call;
        }
        $this->s = auth()->user()->sup()->first();
        $this->a = auth()->user()->adm()->first();
    }
    public function index()
    {
        if (!$this->m && !$this->s && !$this->a) {
            return view('login');
        }

        $orders = $this->os->select('id', 'client_id', 'tec_id','req_date', 'finished')->orderBy('id', 'desc')->get();

        $tecs = Tec::all();

        session()->put('ords', $orders);

        $main = null;
        if (auth()->user()->adm()->first()) {
            $m = auth()->user()->adm()->first()->main;
        }

        $sup = null;
        if (auth()->user()->sup()->first()) {
            $sup = auth()->user()->sup()->first();
        }

        $adm = null;
        if (auth()->user()->adm()->first()) {
            $adm = auth()->user()->adm()->first();
        }

        return view('orders_list' , [
            'orders' => $orders,
            'tecs' => $tecs,
            'main' => $main,
            'sup' => $sup,
            'adm' => $adm
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->a && !$this->o) {
            return view('login');
        }
        
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
        if (!$this->a && !$this->o) {
            return view('login');
        }

        if ($request->client_id == '0') {
            return redirect()->route('orders.create')->with('message', 'Selecione um cliente para prosseguir.');
        }

        //If there is no contact name, then use the name of the client that was selected
        $cont_name_client = $request->req_name;
        if (!$request->req_name) {
            $cont_name_client = Client::find($request->client_id)->contact;
        }

        $tec_id = null;
        if (isset(auth()->user()->tec)) {
            $tec_id = auth()->user()->tec->id;
        }

        //Create new order
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'sector' => $request->sector,
            'req_name' => $cont_name_client,
            'user_id' => auth()->user()->id,
            'tec_id' => $tec_id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);
        
        if ($created) {
            if ($this->o && !$this->a) {
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
        if (!$this->a) {
            return view('login');
        }

        return view('order_delete', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        if (!$this->a) {
            return view('login');
        }

        $clients = Client::select(['id', 'name'])->get();
        $tecs = Tec::all();
        $user = User::select('name')->find($order->user_id);

        $disabled = 'disabled';
        $msg = '';
        if ($this->a) {
            $disabled = '';
            $msg = 'Editar ';
        }
            
        return view('order_edit', [
            'order' => $order,
            'clients' => $clients,
            'tecs' => $tecs,
            'user' => $user,
            'disabled' => $disabled,
            'msg' => $msg
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$this->a) {
            return view('login');
        }

        if ($request->client_id == '0') {
            return redirect()->back()->with('message', 'Selecione um cliente para prosseguir.');
        }

        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id', 'tec_id']));

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
        if (!$this->a) {
            return view('login');
        }

        $order = $this->os->find($id);
        
        foreach ($order->notes as $key => $note) {
            foreach ($note->tecs as $key => $tec) {
                $note_tec = NoteTec::where('note_id', $note->id)->where('tec_id', $tec->id)->first();
                $note_tec->delete();
            }
            $note->delete();
        }

        $deleted = $order->delete();

        if ($deleted) {
            return redirect()->route('orders.index')->with('message', 'Ordem de serviço deletada com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro ao deletar ordem de serviço.');
    }

    public function finish(Order $order): RedirectResponse
    {
        if (!$this->t && !$this->m) {
            return view('login');
        }

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

    public function ord_tec_update(Request $request)
    {
        if (!$this->s && !$this->m) {
            return view('login');
        }

        $ords = session('ords');

        foreach ($ords as  $ord) {
            if ($request->input('ord_'.$ord->id)) {
                $ord->tec_id = $request->input('ord_'.$ord->id);
                $ord = $ord->save();
                if (!$ord) {
                    return redirect()->back()->with('message', 'Erro ao selecionar técnico.'); 
                }
            }
        }

        return redirect()->back()->with('message', 'Técnico selecionado com sucesso.');
    }
}
