<?php

namespace App\Http\Controllers;

use App\Class\TextFormat;
use App\Http\Requests\FormNoteRequest;
use App\Models\Cause;
use App\Models\Defect;
use App\Models\Material;
use App\Models\MaterialNote;
use App\Models\Note;
use App\Models\NoteTec;
use App\Models\NoteType;
use App\Models\Order;
use App\Models\Solution;
use App\Models\Tec;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class NoteController extends Controller
{
    public readonly Note $note;
    private $empit_sign;
    public $t; // user is tec or not
    public $text; // text format

    public function __construct()
    {
        // user is technician or not
        $this->t = auth()->user()->tec()->first();

        // new note
        $this->note = new Note();

        // empty signature
        $this->empit_sign = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAUAAAADICAYAAACZBDirAAAAAXNSR0IArs4c6QAABc5JREFUeF7t1AERAAAIAjHpX9ogPxswPHaOAAECUYFFc4tNgACBM4CegACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAg84oAAyUjb8HgAAAAASUVORK5CYII=';

        // Class with text format functions
        $this->text = new TextFormat;
    }

    public function index()
    {
        if (!$this->t) {
            return view('login');
        }

        $orders = Order::select('id', 'client_id', 'equipment', 'req_descr', 'req_date', 'finished')->where('tec_id', auth()->user()->tec->id)->orderBy('id', 'desc')->simplePaginate(20);

        return view('note.notes_list' , ['orders' => $orders]);
    }

    // Only technicians can access the service order filling form
    public function create($order)
    {
        if (!$this->t) {
            return view('login');
        }

        // Decrypt the order id
        try {
            $order = Order::find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $tecs = Tec::all();

        // Generate arrays with all codes
        $c_l = [
            'n_types' => NoteType::all(),
            'defects' => Defect::all(),
            'causes' => Cause::all(),
            'solutions' => Solution::all(),
            'materials' => Material::all()
        ];

        // Generate tables codes ids lists
        foreach ($c_l as $key => $codes) {
            session()->put($key.'_ids', $codes->pluck('id')->toArray());
        }

        // Get all materials order by name
        $materials = Material::all();
        $materials = $materials->sortBy('description');

        return view('note.note_create', [
            'order' => $order,
            'tecs' => $tecs,
            'types' => $c_l['n_types'],
            'defects' => $c_l['defects'],
            'causes' => $c_l['causes'],
            'solutions' =>  $c_l['solutions'],
            'materials' => $materials
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
            'note_type_id' => $request->input('note_type_id'),
            'defect_id' => $request->input('defect_id'),
            'cause_id' => $request->input('cause_id'),
            'solution_id' => $request->input('solution_id'),
            'services' => $this->text->spaceAfterPunctuation($request->input('services')),
            'date' => $request->input('date'),
            'go_start' => $request->input('go_start'),
            'go_end' => $request->input('go_end'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'back_start' => $request->input('back_start'),
            'back_end' => $request->input('back_end'),
            'food' => $request->input('food'),
            'km_start' => $request->input('km_start'),
            'km_end' => $request->input('km_end'),
            'expense' => $request->input('expense'),
            'obs' => $request->input('obs') ?? 'Sem observaçãos',
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
                    if (!$cr_note_tec2) {
                        return redirect()->back()->with('message', 'Erro ao salvar assinatura do Técnico 02.');
                    }
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
    
            // Save materials in note and validate materials list
            if ($request->input('material_ids_array')) {
                $material_ids = array_unique(explode(",", $request->input('material_ids_array')));
                foreach ($material_ids as $material_id) {
                    $note_material = MaterialNote::create([
                        'note_id' => $created_note->id,
                        'material_id' => $request->input('material_id_'.$material_id),
                        'quantity' => $request->input('material_id_'.$material_id.'_qtd'),
                    ]);
                    if (!$note_material) {
                        return redirect()->back()->with('message', 'Erro ao salvar materiais.');
                    }
                }
            }


            if ($cr_note_tec1 && $updated_os) {
                if ($request->input('finished')) {
                    return redirect()->route('notes.index')->with('message', 'Solicitação de Assistência Técnica finalizada com sucesso.');
                }
                return redirect()->back()->with('message', 'Informações salvas com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao salvar informações.');
    }

    // Show the form for deleting a note on the service orders
    public function show($note)
    {

        // Decrypt the note id
        try {
            $note = $this->note->find(Crypt::decryptString($note));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }
 
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

    public function edit($note)
    {
        // Check if user is logged is a technician
        if (!$this->t) {
            return view('login');
        }

        // Decrypt the note id
        try {
            $note = $this->note->find(Crypt::decryptString($note));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $note->first_tec = Note::find($note->id)->tecs[0];
        if ($this->t->id != $note->first_tec->id) {
            return redirect()->back()->withErrors(['error' => 'Acesso não autorizado para editar anotacões de outro técnico.']);
        }

        // Get the second technician of the note if he exists
        if (isset(Note::find($note->id)->tecs[1])) {
            $note->second_tec = Note::find($note->id)->tecs[1];
        }

        // Get all technicians except the first
        $tecs = Tec::where('id','!=', $note->first_tec->id)->get();

        // Generate tables with all codes list
        $c_l = [
            'n_types' => NoteType::all(),
            'defects' => Defect::all(),
            'causes' => Cause::all(),
            'solutions' => Solution::all(),
            'materials' => Material::all()
        ];

        // Generate tables codes ids lists to validate
        foreach ($c_l as $key => $codes) {
            session()->put($key.'_ids', $codes->pluck('id')->toArray());
        }

        // Remove seconds from requests time format
        $properties = ['go_start', 'go_end', 'start', 'end', 'back_start', 'back_end'];

        foreach ($properties as $property) {
            if ($note->{$property}) {
                $note->{$property} = date_format(date_create($note->{$property}), 'H:i');
            }
        }

        // Convert $note->materials to a JSON string
        if (isset($note->materials)) {
            $materials_json = json_encode($note->materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'description' => $material->description,
                    'quantity' => $material->pivot->quantity,
                    'unit' => $material->unit
                ];
            }));
        }

        // Get all materials order by name
        $materials = Material::all();
        $materials = $materials->sortBy('description');

        return view('note.note_edit', [
            'note' => $note,
            'tecs' => $tecs,
            'types' => $c_l['n_types'],
            'defects' => $c_l['defects'],
            'causes' => $c_l['causes'],
            'solutions' =>  $c_l['solutions'],
            'materials_json' => $materials_json ?? null,
            'materials' => $materials
        ]);
    }

    public function update(FormNoteRequest $request, string $id)
    {      
        // Check if user is logged is a technician
        if (!$this->t) {
            return view('login');
        }

        $note_first_tec = Note::find($id)->tecs[0];

        if ($this->t->id != $note_first_tec->id) {
            return redirect()->back()->withErrors(['error' => 'Acesso não autorizado para editar anotacões de outro técnico.']);
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

            // Update note
            $note = Note::find($id);
            $note->equip_mod = $request->equip_mod;
            $note->equip_id = $request->equip_id;
            $note->equip_type = $request->equip_type;
            $note->note_type_id = $request->note_type_id;
            $note->defect_id = $request->defect_id;
            $note->cause_id = $request->cause_id;
            $note->solution_id = $request->solution_id;
            $note->services = $this->text->spaceAfterPunctuation($request->services);
            $note->date = $request->date;
            $note->go_start = $request->go_start;
            $note->go_end = $request->go_end;
            $note->start = $request->start;
            $note->end = $request->end;
            $note->back_start = $request->back_start;
            $note->back_end = $request->back_end;
            $updated = $note->save();

            // Delete all materials in note
            $material_notes = MaterialNote::where('note_id', $id)->get();
            if (count($material_notes) > 0 || $material_notes != null) {
                foreach ($material_notes as $material_note) {
                    $material_note->delete();
                }
            }

            // Save materials in note and validate materials list
            if ($request->input('material_ids_array')) {
                $material_ids = array_unique(explode(",", $request->input('material_ids_array')));
                foreach ($material_ids as $material_id) {
                    $note_material = MaterialNote::create([
                        'note_id' => $id,
                        'material_id' => $request->input('material_id_'.$material_id),
                        'quantity' => $request->input('material_id_'.$material_id.'_qtd'),
                    ]);
                    if (!$note_material) {
                        return redirect()->back()->with('message', 'Erro ao salvar materiais.');
                    }
                }
            }
    
            if ($updated) {
                return redirect()->back()->with('message', 'Registro de serviço atualizado com sucesso.');
            }
            return redirect()->back()->with('message', 'Erro ao atualizar registro de serviço.');
        }
        return redirect()->back()->with('message', 'Registro pode ser editado apenas pelo técnico executante.');
    }

    public function destroy(Note $note)
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

            // Delete all materials in note
            $material_notes = MaterialNote::where('note_id', $note->id)->get();
            if (count($material_notes) > 0 || $material_notes != null) {
                foreach ($material_notes as $material_note) {
                    $material_note->delete();
                }
            }

            if ($note->delete()) {
                return redirect()->route('notes.create', ['order' => Crypt::encryptString($note->order_id)])
                    ->with('message', 'Registro deletado com sucesso.');
            }

            return redirect()->route('notes.create', ['order' => Crypt::encryptString($note->order_id)])
                ->with('message', 'Erro ao deletar registro.');
        }

        return redirect()->back()->with('message', 'Registro pode ser deletado apenas pelo técnico executante.');
    }

    // Add 30 notes for testing 
    public function add($qtd)
    {
        if (!$this->t) {
            return view('login');
        }

        for ($j=0; $j < $qtd; $j++) { 
            for ($i=0; $i < 30; $i++) { 
                $order_id = 43788 + $j;
                $created_note = $this->note->create([
                    'order_id' => $order_id,
                    'equip_mod' => 'Model order '.$order_id.' note '. $i,
                    'equip_id' => 'Number order '.$order_id.' note '. $i,
                    'equip_type' => 'Type order '.$order_id.' note '. $i,
                    'note_type_id' => 101,
                    'defect_id' => 201,
                    'cause_id' => 399,
                    'solution_id' => 499,
                    'services' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quia vero quae distinctio libero ex? Dolorem tempora adipisci nihil inventore laborum quam numquam eius rem, natus, vitae aperiam, itaque fugiat repudiandae.',
                    'date' => date('Y-m-d'),
                    'go_start' => date('H:i'),
                    'go_end' => date('H:i'),
                    'start' => date('H:i'),
                    'end' => date('H:i'),
                    'back_start' => date('H:i'),
                    'back_end' => date('H:i'),
                ]);
    
                // Create note_tec for first_tec
                if ($created_note) {
                    $cr_note_tec1 = NoteTec::create([
                        'note_id' => $created_note->id,
                        'tec_id' => auth()->user()->tec()->first()->id,
                        'signature' => 'data:image/png;base64,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',
                    ]);
    
                    // Create note_tec for second_tec if he is not 0
                    if ($created_note) {
                        $cr_note_tec2 = NoteTec::create([
                            'note_id' => $created_note->id,
                            'tec_id' => auth()->user()->tec()->first()->id,
                            'signature' => 'data:image/png;base64,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' ?? null,
                        ]);
                    }
                }
        
                // Update client info and finshed status on order
                $os = Order::find($order_id);
                $os->cl_name = 'Client order '.$order_id;
                $os->cl_function = "Function ".$order_id;
                $os->cl_contact = '015 15 4433322'.$order_id;
                $os->cl_date = \Carbon\Carbon::now()->format('Y-m-d');
                $os->cl_sign = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAUAAAADICAYAAACZBDirAAAAAXNSR0IArs4c6QAABc5JREFUeF7t1AERAAAIAjHpX9ogPxswPHaOAAECUYFFc4tNgACBM4CegACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAgYQD9AgEBWwABmqxecAAED6AcIEMgKGMBs9YITIGAA/QABAlkBA5itXnACBAygHyBAICtgALPVC06AgAH0AwQIZAUMYLZ6wQkQMIB+gACBrIABzFYvOAECBtAPECCQFTCA2eoFJ0DAAPoBAgSyAgYwW73gBAg84oAAyUjb8HgAAAAASUVORK5CYII=';
                $os->finished = 1;
                $updated_os = $os->save();
            }
        }
    
        if ($cr_note_tec1 && $updated_os) {
            return redirect()->route('notes.index')->with('message', 'Solicitações de Assistência Técnica para testes finalizadas com sucesso.');
        }
        return redirect()->back()->with('message', 'Erro ao salvar informações para testes.');
    }
}
