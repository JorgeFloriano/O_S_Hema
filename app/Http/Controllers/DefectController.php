<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Defect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;

class DefectController extends Controller
{

    public readonly Defect $defect;

    public function __construct()
    {
        $this->defect = new Defect();
    }
    public function index()
    {
        Gate::authorize('check-permission', ['codes', 1]);

        session()->put('table', 'defects');

        return redirect()->route('defects.list', 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        if ($opt == 0) {
            $defects = $this->defect->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'defects.restore';
        } else {
            $defects = $this->defect->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
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
        Gate::authorize('check-permission', ['codes', 2]);

        return view('codes.defect.defect_create');
    }

    public function store(FormCodeRequest $request)
    {
        Gate::authorize('check-permission', ['codes', 2]);

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
        Gate::authorize('check-permission', ['codes', 1]);

        return view('codes.defect.defect_delete', ['defect' => $defect]);
    }

    public function edit($defect)
    {
        Gate::authorize('is-main-adm');

        try {
            $defect = $this->defect->find(Crypt::decryptString($defect));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        return view('codes.defect.defect_edit', ['defect' => $defect]);
    }

    public function update(FormCodeRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->defect->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('defects.index')->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->route('defects.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $deleted = $this->defect->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('defects.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('defects.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $restored = $this->defect->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('defects.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('defects.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $deleted = $this->defect->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('defects.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('defects.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
