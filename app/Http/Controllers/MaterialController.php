<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormMaterialRequest;
use App\Models\Material;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;

class MaterialController extends Controller
{

    public readonly Material $material;
    public readonly array $units;

    public function __construct()
    {
        $this->material = new Material();
        $this->units = ['Pc', 'M', 'Kg', 'Litro', 'M2', 'M3', 'Kit'];
    }
    public function index()
    {
        Gate::authorize('check-permission', ['materials', 1, 'materials.index function']);

        session()->put('table', 'materials');

        return redirect()->route('materials.list', 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['materials', 1, 'materials.list function']);

        if ($opt == 0) {
            $materials = $this->material->onlyTrashed()->orderBy('description')->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'materials.restore';
        } else {
            $materials = $this->material->orderBy('description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
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
        Gate::authorize('check-permission', ['materials', 2]);

        return view('material.material_create', ['units' => $this->units]);
    }

    public function store(FormMaterialRequest $request)
    {
        Gate::authorize('check-permission', ['materials', 2]);

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
        Gate::authorize('check-permission', ['materials', 1]);

        return view('material.material_delete', ['material' => $material]);
    }

    public function edit($material)
    {
        Gate::authorize('is-main-adm');

        try {
            $material = $this->material->find(Crypt::decryptString($material));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        return view('material.material_edit', [
            'material' => $material,
            'units' => $this->units
        ]);
    }

    public function update(FormMaterialRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->material->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('materials.index')->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->route('materials.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('authorize', ['materials', 2]);

        $deleted = $this->material->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('materials.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('materials.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['materials', 2]);

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
        Gate::authorize('check-permission', ['materials', 2]);

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
