<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Solution;

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

        return redirect()->route('solutions.list' , 1);
    }

    public function list(bool $opt)
    {
        
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        if ($opt == 0) {
            $solutions = $this->solution->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Mostrar Ativos';
            $btn_color = 'btn-success';
            $route = 'solutions.restore';
        } else {
            $solutions = $this->solution->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Mostrar Desativados';
            $btn_color = 'btn-danger';
            $route = 'solutions.desativate';
        }

        return view('codes.solution.solutions_list', [
            'solutions' => $solutions,
            'opt' => $opt,
            'msg' => $msg,
            'cond' => $cond,
            'title' => $title,
            'btn_color' => $btn_color,
            'route' => $route
        ]);
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

    
    public function update(FormCodeRequest $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();
        
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

    public function restore(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        $restored = $this->solution->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('solutions.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('solutions.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->solution->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('solutions.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('solutions.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
