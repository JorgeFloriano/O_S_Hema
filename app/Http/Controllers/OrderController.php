<?php

namespace App\Http\Controllers;

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
        return session()->get('message');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $created = $this->os->create([
            'client_id' => 1,
            'type' => 0,
            'finished' => $request->input('finished'),
            'equipment' => "Camera 77",
            'req_date' => "1000-01-01 00:00:00",
            'req_descr' => "Câmera sem gravação",
        ]);
        if ($created) {
            return redirect()->route('orders.index')->with('message', 'Cadastro realizado com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro no cadastro.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = new Note();
        $created_note = $note->create([
        'order_id' => $id,
        'equip_mod' => $request->input('equip_mod'),
        'equip_id' => $request->input('equip_id'),
        'equip_type' => $request->input('equip_type'),
        'situation' => $request->input('situation'),
        'cause' => $request->input('cause'),
        'services' => $request->input('services'),
        'date' => $request->input('date'),
        'go_start' => $request->input('go_start'),
        'go_end' => $request->input('go_end'),
        'start' => $request->input('start'),
        'end' => $request->input('end'),
        'back_start' => $request->input('back_start'),
        'back_end' => $request->input('back_end'),
        'first_tec' => $request->input('first_tec'),
        'sign_t_1' => $request->input('sign_t_1'),
        'second_tec' => $request->input('second_tec'),
        'sign_t_2' => $request->input('sign_t_2'),
        ]);

        $os = Order::find($id);
        $os->finished = $request->input('finished');
        $updated_os = $os->save();

        if ($created_note && $updated_os) {
            return redirect()->route('orders.index')->with('message', 'Cadastro realizado com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro no cadastro.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
