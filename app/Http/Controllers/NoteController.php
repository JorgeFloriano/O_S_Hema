<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Order;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public readonly Order $os;
    public readonly Note $note;

    public function __construct()
    {
        $this->os = new Order();
        $this->note = new Note();
    }

    public function index()
    {
        $orders = $this->os->select('id', 'client_id','req_date')->get();

        return view('notes_list' , ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('note_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $created_note = $this->note->create([
            'order_id' => $request->input('order_id'),
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
    
            $os = Order::find($request->input('order_id'));
            $os->finished = $request->input('finished');
            $updated_os = $os->save();
    
            if ($created_note && $updated_os) {
                return redirect()->back()->with('message', 'Informações salvas com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao salvar informações.');
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
    public function edit(Order $note)
    {
        return view('note_create', ['order' => $note]);
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
