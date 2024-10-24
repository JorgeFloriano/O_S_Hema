<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Cause;

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

        return redirect()->route('causes.list' , 1);
    }

    public function list(bool $opt)
    {
        
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        if ($opt == 0) {
            $causes = $this->cause->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Mostrar Ativos';
            $btn_color = 'btn-success';
            $route = 'causes.restore';
        } else {
            $causes = $this->cause->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Mostrar Desativados';
            $btn_color = 'btn-danger';
            $route = 'causes.desativate';
        }

        return view('codes.cause.causes_list', [
            'causes' => $causes,
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

    public function restore(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        $restored = $this->cause->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('causes.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('causes.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->cause->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('causes.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('causes.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}