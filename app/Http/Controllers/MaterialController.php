<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormMaterialRequest;
use App\Models\Material;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MaterialController extends Controller
{
   
    public readonly Material $material;

    public function __construct()
    {
        $this->material = new Material();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'materials');

        return redirect()->route('materials.list' , 1);
    }

    public function list(bool $opt)
    {
        
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        if ($opt == 0) {
            $materials = $this->material->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-success';
            $route = 'materials.restore';
        } else {
            $materials = $this->material->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-danger';
            $route = 'materials.desativate';
        }

        return view('material.materials_list', [
            'materials' => $materials,
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

        return view('material.material_create');
    }

   
    public function store(FormMaterialRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();

        $created = $this->material->create([
            'id' => $request->id,
            'description' => $request->description,
            'unit' => $request->unit
        ]);
        if ($created) {
            return redirect()->route('materials.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('materials.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Material $material)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('material.material_delete', ['material' => $material]);
    }

    
    public function edit($material)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        try {
            $material = $this->material->find(Crypt::decryptString($material));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        return view('material.material_edit', ['material' => $material]);
    }

    
    public function update(FormMaterialRequest $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();
        
        $updated = $this->material->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('materials.index')->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->route('materials.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->material->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('materials.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('materials.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $restored = $this->material->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('materials.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('materials.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }
        
        $deleted = $this->material->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('materials.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('materials.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
