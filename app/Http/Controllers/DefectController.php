<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Defect;
use Illuminate\Http\Request;

class DefectController extends Controller
{
   
    public readonly Defect $defect;

    public function __construct()
    {
        $this->defect = new Defect();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'defects');
        $defects = $this->defect->select('id', 'description')->simplePaginate(10);

        return view('codes.defect.defects_list', ['defects' => $defects]);
    }

    public function create()
    {    
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.defect.defect_create');
    }

   
    public function store(FormCodeRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();

        $created = $this->defect->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('defects.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('defects.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Defect $defect)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('codes.defect.defect_delete', ['defect' => $defect]);
    }

    
    public function edit(Defect $defect)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.defect.defect_edit', ['defect' => $defect]);
    }

    
    public function update(Request $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $updated = $this->defect->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('defects.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('defects.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->defect->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('defects.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('defects.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
