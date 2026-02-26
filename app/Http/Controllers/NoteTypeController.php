<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\NoteType;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NoteTypeController extends Controller implements HasMiddleware
{

    public readonly NoteType $note_type;

    public static function middleware(): array
    {
        return [
            // Visualização de códigos (Nível 1)
            new Middleware('can:view-codes', only: ['index', 'list', 'show']),

            // Gerenciamento básico (Nível 2) - Criar, Deletar, Restaurar
            new Middleware('can:manage-codes', only: ['create', 'store', 'destroy', 'restore', 'desativate']),

            // Edição de códigos existentes - Restrito ao Administrador Principal
            new Middleware('can:is-main-adm', only: ['edit', 'update']),
        ];
    }

    public function __construct()
    {
        $this->note_type = new NoteType();
    }
    public function index()
    {
        Gate::authorize('check-permission', ['codes', 1]);

        session()->put('table', 'note_types');

        return redirect()->route('note_types.list', 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        if ($opt == 0) {
            $note_types = $this->note_type->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'note_types.restore';
        } else {
            $note_types = $this->note_type->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
            $route = 'note_types.desativate';
        }

        return view('codes.note_type.note_types_list', [
            'note_types' => $note_types,
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

        return view('codes.note_type.note_type_create');
    }

    public function store(FormCodeRequest $request)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $request->validated();

        $created = $this->note_type->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('note_types.list', 1)->with('message', 'Código cadastrado com sucesso.');
        }
        logger_main('error', 'Note type not created');
        return redirect()->route('note_types.list', 1)->with('message', 'Erro ao cadastrar código.');
    }

    public function show(NoteType $note_type)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        return view('codes.note_type.note_type_delete', ['note_type' => $note_type]);
    }

    public function edit($note_type)
    {
        Gate::authorize('is-main-adm');

        try {
            $note_type = $this->note_type->find(Crypt::decryptString($note_type));
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (note_type/edit).');
            echo 'Erro de desencriptação.';
            die;
        }

        return view('codes.note_type.note_type_edit', ['note_type' => $note_type]);
    }

    public function update(FormCodeRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->note_type->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('note_types.list', 1)->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        logger_main('error', 'Note type not updated');
        return redirect()->route('note_types.list', 1)->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $deleted = $this->note_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('note_types.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        logger_main('error', 'Note type not deleted');
        return redirect()->route('note_types.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (note_type/restore).');
            echo 'Erro de desencriptação.';
            die;
        }

        $restored = $this->note_type->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('note_types.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        logger_main('error', 'Note type not restored');
        return redirect()->route('note_types.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (note_type/desativate).');
            echo 'Erro de desencriptação.';
            die;
        }

        $deleted = $this->note_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('note_types.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        logger_main('error', 'Note type not deleted');
        return redirect()->route('note_types.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
