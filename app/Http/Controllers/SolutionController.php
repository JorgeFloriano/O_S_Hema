<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\Solution;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SolutionController extends Controller implements HasMiddleware
{

    public readonly Solution $solution;

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
        $this->solution = new Solution();
    }
    public function index()
    {
        Gate::authorize('check-permission', ['codes', 1]);

        session()->put('table', 'solutions');

        return redirect()->route('solutions.list', 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        if ($opt == 0) {
            $solutions = $this->solution->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'solutions.restore';
        } else {
            $solutions = $this->solution->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
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
        Gate::authorize('check-permission', ['codes', 2]);

        return view('codes.solution.solution_create');
    }

    public function store(FormCodeRequest $request)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $request->validated();

        $created = $this->solution->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('solutions.list', 1)->with('message', 'Código cadastrado com sucesso.');
        }

        logger_main('error', 'Solution not created');
        return redirect()->route('solutions.list', 1)->with('message', 'Erro ao cadastrar código.');
    }

    public function show(Solution $solution)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        return view('codes.solution.solution_delete', ['solution' => $solution]);
    }

    public function edit($solution)
    {
        Gate::authorize('is-main-adm');

        try {
            $solution = $this->solution->find(Crypt::decryptString($solution));
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (solution/edit).');
            echo 'Erro de desencriptação.';
            die;
        }

        return view('codes.solution.solution_edit', ['solution' => $solution]);
    }

    public function update(FormCodeRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->solution->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('solutions.list', 1)->with('message', 'Cadastro atualizado com sucesso.');
        }

        logger_main('error', 'Solution not updated');
        return redirect()->route('solutions.list', 1)->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $deleted = $this->solution->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('solutions.index')->with('message', 'Cadastro deletado com sucesso.');
        }

        logger_main('error', 'Solution not deleted');
        return redirect()->route('solutions.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (solution/restore).');
            echo 'Erro de desencriptação.';
            die;
        }

        $restored = $this->solution->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('solutions.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }

        logger_main('error', 'Solution not restored');
        return redirect()->route('solutions.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (solution/desativate).');
            echo 'Erro de desencriptação.';
            die;
        }

        $deleted = $this->solution->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('solutions.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }

        logger_main('error', 'Solution not deleted');
        return redirect()->route('solutions.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
