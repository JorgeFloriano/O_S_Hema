<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCodeRequest;
use App\Models\OrderType;
use Illuminate\Http\Request;

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
        $order_types = $this->order_type->select('id', 'description')->simplePaginate(10);

        return view('codes.order_type.order_types_list', ['order_types' => $order_types]);
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

    
    public function update(Request $request, string $id)
    {
        if (session('main') !== auth()->user()->id) {
            return view('login');
        }
        
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
            return redirect()->route('order_types.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('order_types.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
