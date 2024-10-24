<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\OrderType;

class OrderTypeController extends Controller
{
   
    public readonly OrderType $order_type;

    public function __construct()
    {
        $this->order_type = new OrderType();
    }
    public function index()
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        session()->put('table', 'order_types');

        return redirect()->route('order_types.list' , 1);
    }

    public function list(bool $opt)
    {
        
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        if ($opt == 0) {
            $order_types = $this->order_type->select('id', 'description')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $msg = 'Desativados';
            $cond = 'Ativar';
            $title = 'Mostrar Ativos';
            $btn_color = 'btn-success';
            $route = 'order_types.restore';
        } else {
            $order_types = $this->order_type->select('id', 'description')->simplePaginate(20);
            $opt = 0;
            $msg = 'Ativos';
            $cond = 'Desativar';
            $title = 'Mostrar Desativados';
            $btn_color = 'btn-danger';
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
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.order_type.order_type_create');
    }

   
    public function store(FormCodeRequest $request)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

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
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.order_type.order_type_delete', ['order_type' => $order_type]);
    }

    
    public function edit(OrderType $order_type)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }

        return view('codes.order_type.order_type_edit', ['order_type' => $order_type]);
    }

    
    public function update(FormCodeRequest $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $request->validated();
        
        $updated = $this->order_type->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->route('order_types.index')->with('message', 'Cadastro de causa atualizado com sucesso.');
        }
        return redirect()->route('order_types.index')->with('message', 'Erro ao atualizar cadastro.');
    }

    
    public function destroy(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->order_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('order_types.list', 1)->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('order_types.list', 1)->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        $restored = $this->order_type->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('order_types.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('order_types.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->order_type->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('order_types.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('order_types.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
