<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public readonly Note $note;
    private $empit_sign;

    public function __construct()
    {
        $this->note = new Note();
        $this->empit_sign = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAUAAAADICAYAAACZBDirAAAAAXNSR0IArs4c6QAABc5JREFUeF7t1AERAAAIAjHpX9ogPxswPHaOAAECUYFFc4tNgACBM4CegACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAg84oAAyUjb8HgAAAAASUVORK5CYII=';
    }

    public function index()
    {
        $orders = Order::select('id', 'client_id','req_date')->where('user_id', auth()->user()->id)->get();

        return view('notes_list' , ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Order $order)
    {
        foreach ($order->notes as $note) {
            $note->first_tec = User::find($note->first_tec);
            $note->second_tec = User::find($note->second_tec);
        }

        $writer = User::select('id', 'name')->where('type', 2)->find($order->writer_id);
        $tecs = User::select('id', 'name')->where('type', 3)->get();

        return view('note_create', [
            'order' => $order,
            'tecs' => $tecs,
            'writer' => $writer
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!isset($request->sign_t_1) || $request->sign_t_1 == $this->empit_sign) {
            return redirect()->back()->with('message', 'Informações não podem ser salvas sem assinatura de um Técnico.');
        }

        $second_tec = $request->second_tec;
        if ($request->first_tec == $second_tec) {
            $second_tec = 0;
        }

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
            'second_tec' => $second_tec,
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
    public function show(Note $note)
    {
        $note->first_tec = User::select('id', 'name')->find($note->first_tec);
        $note->second_tec = User::select('id', 'name')->find($note->second_tec);
        $writer = User::select('id', 'name')->where('type', 2)->find($note->order->writer_id);

        $msg = 'Deletar';
        if (auth()->user()->id != $note->first_tec->id) {
            $msg = 'Informações do';
        }

        return view('note_delete', [
            'note' => $note,
            'msg' => $msg,
            'writer' => $writer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        
        $note->first_tec = User::select('id', 'name')->find($note->first_tec);
        $note->second_tec = User::select('id', 'name')->find($note->second_tec);
        $writer = User::select('id', 'name')->where('type', 2)->find($note->order->writer_id);

        
        $tecs = User::select('id', 'name')
            ->where('type', 3)
            ->where('id', '!=', $note->first_tec->id)
            ->get();

        return view('note_edit', [
            'note' => $note,
            'tecs' => $tecs,
            'writer' => $writer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {      
        if (auth()->user()->id == $request->first_tec) {
            if (!isset($request->sign_t_1) || $request->sign_t_1 == $this->empit_sign) {
                return redirect()->back()->with('message', 'Informações não podem ser salvas sem assinatura de um Técnico.');
            }
            $updated = $this->note->where('id', $id)->update($request->except(['_token', '_method', 'submit_button']));
    
            if ($updated) {
                return redirect()->back()->with('message', 'Registro de serviço atualizada com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao atualizar registro de serviço.');
        }
        return redirect()->back()->with('message', 'Registro pode ser editado apenas pelo técnico executante.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if (auth()->user()->id == $note->first_tec) {
            $deleted = $this->note->where('id', $note->id)->delete();
    
            if ($deleted) {
                return redirect()->route('notes.create', ['order' => $note->order->id])->with('message', 'Registro deletado com sucesso.');
            }
            return redirect()->route('notes.create', ['order' => $note->order->id])->with('message', 'Erro ao deletar registro.');
        }
        return redirect()->back()->with('message', 'Registro pode ser deletado apenas pelo técnico executante.');
    }
}
