<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Cause;
use App\Class\CryptMsg;
use Illuminate\Support\Facades\Gate;

class CauseController extends Controller
{

    public readonly Cause $cause;
    public $crypt;

    public function __construct()
    {
        $this->cause = new Cause();
        $this->crypt = new CryptMsg();
    }
    public function index()
    {
        Gate::authorize('check-permission', ['codes', 1]);

        session()->put('table', 'causes');

        return redirect()->route('causes.list', 1);
    }

    public function list(bool $opt)
    {

        Gate::authorize('check-permission', ['codes', 1]);

        if ($opt == 0) {
            $causes = $this->cause->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'causes.restore';
        } else {
            $causes = $this->cause->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
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
        Gate::authorize('check-permission', ['codes', 2]);

        return view('codes.cause.cause_create');
    }
    public function store(FormCodeRequest $request)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $request->validated();

        $created = $this->cause->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('causes.index')->with('message', 'Código cadastrado com sucesso.');
        }
        logger_main('error', 'Cause not created');
        return redirect()->route('causes.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Cause $cause)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        return view('codes.cause.cause_delete', ['cause' => $cause]);
    }

    public function edit($cause)
    {
        Gate::authorize('is-main-adm');

        $decrypt_id = $this->crypt->tryDecrypt($cause);
        if ($decrypt_id) {
            return view('codes.cause.cause_edit', ['cause' => $this->cause->where('id', $decrypt_id)->first()]);
        }
        logger_main('error', 'Decryption error (cause/edit).');
        return redirect()->back()->withErrors('Erro de desencriptação.');
    }

    public function update(FormCodeRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->cause->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('causes.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        logger_main('error', 'Cause not updated');
        return redirect()->route('causes.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $deleted = $this->cause->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('causes.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        logger_main('error', 'Cause not deleted');
        return redirect()->route('causes.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $decrypt_id = $this->crypt->tryDecrypt($id);
        if (!$decrypt_id) {
            logger_main('error', 'Decryption error (cause/restore).');
            return redirect()->back()->withErrors('Erro de desencriptação.');
        }

        $restored = $this->cause->where('id', $decrypt_id)->restore();

        if ($restored) {
            return redirect()->route('causes.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('causes.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $decrypt_id = $this->crypt->tryDecrypt($id);
        if (!$decrypt_id) {
            return redirect()->back()->withErrors('Erro de desencriptação.');
        }

        $deleted = $this->cause->where('id', $decrypt_id)->delete();

        if ($deleted) {
            return redirect()->route('causes.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        logger_main('error', 'Cause not deleted');
        return redirect()->route('causes.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
