<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
   
    public readonly Solution $solution;

    public function __construct()
    {
        $this->solution = new Solution();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'solutions');
        $solutions = $this->solution->select('id', 'description')->simplePaginate(10);

        return view('codes.solution.solutions_list', ['solutions' => $solutions]);
    }

    public function create()
    {    
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.solution.solution_create');
    }

   
    public function store(FormCodeRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();

        $created = $this->solution->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('solutions.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('solutions.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Solution $solution)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('codes.solution.solution_delete', ['solution' => $solution]);
    }

    
    public function edit(Solution $solution)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.solution.solution_edit', ['solution' => $solution]);
    }

    
    public function update(Request $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $updated = $this->solution->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('solutions.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('solutions.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->solution->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('solutions.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('solutions.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
