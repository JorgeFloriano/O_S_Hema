<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\NoteType;
use Illuminate\Http\Request;

class NoteTypeController extends Controller
{
   
    public readonly NoteType $note_type;

    public function __construct()
    {
        $this->note_type = new NoteType();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'note_types');
        $note_types = $this->note_type->select('id', 'description')->simplePaginate(10);

        return view('codes.note_type.note_types_list', ['note_types' => $note_types]);
    }

    public function create()
    {    
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.note_type.note_type_create');
    }

   
    public function store(FormCodeRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();

        $created = $this->note_type->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('note_types.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('note_types.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(NoteType $note_type)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('codes.note_type.note_type_delete', ['note_type' => $note_type]);
    }

    
    public function edit(NoteType $note_type)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.note_type.note_type_edit', ['note_type' => $note_type]);
    }

    
    public function update(Request $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $updated = $this->note_type->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('note_types.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('note_types.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->note_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('note_types.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('note_types.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
