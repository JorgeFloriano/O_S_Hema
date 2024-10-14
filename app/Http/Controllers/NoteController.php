<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormNoteRequest;
use App\Models\Note;
use App\Models\NoteTec;
use App\Models\Order;
use App\Models\Tec;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public readonly Note $note;
    private $empit_sign;
    public $t; // user is tec or not

    public function __construct()
    {
        $this->t = auth()->user()->tec()->first();
        $this->note = new Note();
        $this->empit_sign = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAUAAAADICAYAAACZBDirAAAAAXNSR0IArs4c6QAABc5JREFUeF7t1AERAAAIAjHpX9ogPxswPHaOAAECUYFFc4tNgACBM4CegACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAg84oAAyUjb8HgAAAAASUVORK5CYII=';
    }

    public function index()
    {
        if (!$this->t) {
            return view('login');
        }

        $orders = Order::select('id', 'client_id','req_date', 'finished')->where('tec_id', auth()->user()->tec->id)->orderBy('id', 'desc')->simplePaginate(10);

        return view('note.notes_list' , ['orders' => $orders]);
    }

    // Only technicians can access the service order filling form
    public function create(Order $order)
    {
        if (!$this->t) {
            return view('login');
        }

        $tecs = Tec::all();

        return view('note.note_create', [
            'order' => $order,
            'tecs' => $tecs,
        ]);
    }

    // Only technicians can save notes on the service orders
    public function store(FormNoteRequest $request)
    {
        if (!$this->t) {
            return view('login');
        }

        $request->validated();

        // Form info can't be saved without first technician signature
        if (!isset($request->sign_t_1) || $request->sign_t_1 == $this->empit_sign) {
            return redirect()->back()->with('message', 'Informações não podem ser salvas sem assinatura de um Técnico.');
        }

        // If there is no second_tec, set it to 0
        $second_tec = $request->first_tec == $request->second_tec ? '0' : $request->second_tec;

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
            'food' => $request->input('food') ?? 0,
            'km_start' => $request->input('km_start'),
            'km_end' => $request->input('km_end'),
            'expense' => $request->input('expense') ?? 0,
            'obs' => $request->input('obs'),
            ]);

            // Create note_tec for first_tec
            if ($created_note) {
                $cr_note_tec1 = NoteTec::create([
                    'note_id' => $created_note->id,
                    'tec_id' => $request->input('first_tec'),
                    'signature' => $request->input('sign_t_1'),
                ]);

                // Create note_tec for second_tec if he is not 0
                if ($second_tec != '0') {
                    $cr_note_tec2 = NoteTec::create([
                        'note_id' => $created_note->id,
                        'tec_id' => $second_tec,
                        'signature' => $request->input('sign_t_2') ?? null,
                    ]);
                }
            }
    
            // Update client info and finshed status on order
            $os = Order::find($request->input('order_id'));
            $os->cl_name = $request->input('cl_name');
            $os->cl_function = $request->input('cl_function');
            $os->cl_contact = $request->input('cl_contact');
            $os->cl_date = \Carbon\Carbon::now()->format('Y-m-d');
            $os->cl_sign = $request->input('cl_sign');
            $os->finished = $request->input('finished');
            $updated_os = $os->save();
    
            if ($cr_note_tec1 && $updated_os) {
                if ($request->input('finished')) {
                    return redirect()->route('notes.index')->with('message', 'Solicitação de Serviço finalizada com sucesso.');
                }
                return redirect()->back()->with('message', 'Informações salvas com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao salvar informações.');
    }

    // Show the form for deleting a note on the service orders
    public function show(Note $note)
    {
        // Get the first technician of the note
        $note->first_tec = Note::find($note->id)->tecs[0];

        // Get the second technician of the note if he exists
        if (isset(Note::find($note->id)->tecs[1])) {
            $note->second_tec = Note::find($note->id)->tecs[1];
        }

        $msg = 'Deletar';
        if (!isset(auth()->user()->tec)) {
            $msg = 'Informações do';
        } else {
            if (auth()->user()->tec->id != $note->first_tec->id) {
                $msg = 'Informações do';
            }
        }

        return view('note.note_delete', [
            'note' => $note,
            'msg' => $msg,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        $note->first_tec = Note::find($note->id)->tecs[0];

        if (isset(Note::find($note->id)->tecs[1])) {
            $note->second_tec = Note::find($note->id)->tecs[1];
        }

        $tecs = Tec::where('id','!=', $note->first_tec->id)->get();

        // Remove seconds from requests time format
        $note->go_start = date_format(date_create($note->go_start), 'H:i');
        $note->go_end = date_format(date_create($note->go_end), 'H:i');
        $note->start = date_format(date_create($note->start), 'H:i');
        $note->end = date_format(date_create($note->end), 'H:i');
        $note->back_start = date_format(date_create($note->back_start), 'H:i');
        $note->back_end = date_format(date_create($note->back_end), 'H:i');

        return view('note.note_edit', [
            'note' => $note,
            'tecs' => $tecs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormNoteRequest $request, string $id)
    {      
        if (!$this->t) {
            return view('login');
        }

        $request->validated();

        $note_tec1 = NoteTec::where('note_id', $id)->get()[0];
        $note_tec1->signature = $request->sign_t_1;
        $s_t1_save = $note_tec1->save();
        if (!$s_t1_save) {
            return redirect()->back()->with('message', 'Erro ao atualizar assinatura do Técnico 01.');
        }

        if ($request->second_tec != 0) {
            if (isset(NoteTec::where('note_id', $id)->get()[1])) {
                $note_tec2 = NoteTec::where('note_id', $id)->get()[1];
                $note_tec2->tec_id = $request->second_tec;
                $note_tec2->signature = $request->sign_t_2;
                $n_t2_save = $note_tec2->save();
                if (!$n_t2_save) {
                    return redirect()->back()->with('message', 'Erro ao atualizar registro do Técnico 02.');
                }
            } else {
                $note_tec2 = NoteTec::create([
                    'note_id' => $id,
                    'tec_id' => $request->second_tec,
                    'signature' => $request->sign_t_2,
                ]);
                if (!$note_tec2) {
                    return redirect()->back()->with('message', 'Erro ao criar registro do Técnico 02.');
                }
            }
        }

        if (auth()->user()->tec->id == $request->first_tec) {
            if (!isset($request->sign_t_1) || $request->sign_t_1 == $this->empit_sign) {
                return redirect()->back()->with('message', 'Informações não podem ser salvas sem assinatura de um Técnico.');
            }

            $updated = $this->note->where('id', $id)->update($request->except(['_token', '_method', 'submit_button', 'first_tec', 'second_tec', 'sign_t_1', 'sign_t_2', 'finished']));
    
            if ($updated) {
                return redirect()->back()->with('message', 'Registro de serviço atualizado com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao atualizar registro de serviço.');
        }
        return redirect()->back()->with('message', 'Registro pode ser editado apenas pelo técnico executante.');
    }
    /**
     * Mark the specified Note as finished.
     */

    public function destroy(Note $note): RedirectResponse
    {
        if (!$this->t) {
            return view('login');
        }

        $firstTec = $note->tecs->first();

        if (auth()->user()->tec->id === $firstTec->id) {
            $noteTecs = NoteTec::where('note_id', $note->id)->get();
            foreach ($noteTecs as $noteTec) {
                $noteTec->delete();
            }

            if ($note->delete()) {
                return redirect()->route('notes.create', ['order' => $note->order->id])
                    ->with('message', 'Registro deletado com sucesso.');
            }

            return redirect()->route('notes.create', ['order' => $note->order->id])
                ->with('message', 'Erro ao deletar registro.');
        }

        return redirect()->back()->with('message', 'Registro pode ser deletado apenas pelo técnico executante.');
    }
}
