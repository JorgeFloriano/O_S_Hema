<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Cause;
use Illuminate\Http\Request;

class CauseController extends Controller
{
   
    public readonly Cause $cause;

    public function __construct()
    {
        $this->cause = new Cause();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        session()->put('table', 'causes');
        $causes = $this->cause->select('id', 'description')->simplePaginate(10);

        return view('codes.cause.causes_list', ['causes' => $causes]);
    }

    public function create()
    {    
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.cause.cause_create');
    }

   
    public function store(FormCodeRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();

        $created = $this->cause->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('causes.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('causes.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Cause $cause)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('codes.cause.cause_delete', ['cause' => $cause]);
    }

    
    public function edit(Cause $cause)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.cause.cause_edit', ['cause' => $cause]);
    }

    
    public function update(FormCodeRequest $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'causes');
        $request->validated();
        
        $updated = $this->cause->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('causes.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('causes.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->cause->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('causes.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('causes.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
