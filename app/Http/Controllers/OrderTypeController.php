<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\OrderType;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;

class OrderTypeController extends Controller
{

    public readonly OrderType $order_type;

    public function __construct()
    {
        $this->order_type = new OrderType();
    }
    public function index()
    {
        Gate::authorize('check-permission', ['codes', 1]);

        session()->put('table', 'order_types');

        return redirect()->route('order_types.list', 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        if ($opt == 0) {
            $order_types = $this->order_type->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Ativos';
            $btn_color = 'btn-outline-primary';
            $route = 'order_types.restore';
        } else {
            $order_types = $this->order_type->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Desativados';
            $btn_color = 'btn-outline-primary';
            $route = 'order_types.desativate';
        }

        return view('codes.order_type.order_types_list', [
            'order_types' => $order_types,
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

        return view('codes.order_type.order_type_create');
    }

    public function store(FormCodeRequest $request)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $request->validated();

        $created = $this->order_type->create([
            'id' => $request->id,
            'description' => $request->description,
        ]);
        if ($created) {
            return redirect()->route('order_types.index')->with('message', 'Código cadastrado com sucesso.');
        }
        return redirect()->route('order_types.index')->with('message', 'Erro ao cadastrar código.');
    }

    public function show(OrderType $order_type)
    {
        Gate::authorize('check-permission', ['codes', 1]);

        return view('codes.order_type.order_type_delete', ['order_type' => $order_type]);
    }

    public function edit($order_type)
    {
        Gate::authorize('is-main-adm');

        try {
            $order_type = $this->order_type->find(Crypt::decryptString($order_type));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        return view('codes.order_type.order_type_edit', ['order_type' => $order_type]);
    }

    public function update(FormCodeRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();

        $updated = $this->order_type->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('order_types.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('order_types.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['codes', 2]);

        $deleted = $this->order_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('order_types.list', 1)->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('order_types.list', 1)->with('message', 'Erro ao deletar cadastro.');
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

        $restored = $this->order_type->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('order_types.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('order_types.list', 0)->with('message', 'Erro ao restaurar cadastro.');
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

        $deleted = $this->order_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('order_types.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('order_types.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
