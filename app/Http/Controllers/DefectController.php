<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Defect;

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

        return redirect()->route('defects.list' , 1);
    }

    public function list(bool $opt)
    {
        
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        if ($opt == 0) {
            $defects = $this->defect->select('id', 'description')->onlyTrashed()->simplePaginate(10);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Mostrar Ativos';
            $btn_color = 'btn-success';
            $route = 'defects.restore';
        } else {
            $defects = $this->defect->select('id', 'description')->simplePaginate(10);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Mostrar Desativados';
            $btn_color = 'btn-danger';
            $route = 'defects.desativate';
        }

        return view('codes.defect.defects_list', [
            'defects' => $defects,
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

    
    public function update(FormCodeRequest $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();
        
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

    public function restore(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        $restored = $this->defect->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('defects.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('defects.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->defect->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('defects.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('defects.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
