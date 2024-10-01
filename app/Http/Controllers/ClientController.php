<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public readonly Client $client;
    public $m; // user is adm main or not

    public function __construct()
    {
        $this->client = new Client();
        if (isset(auth()->user()->adm)) {   
            $this->m = auth()->user()->adm()->first()->main;
        }
    }
    
    public function index()
    {
        if (!$this->m) {
            return view('login');
        }

        $clients = $this->client->select('id', 'name','unit')->get();

        return view('clients_list' , ['clients' => $clients]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->m) {
            return view('login');
        }
        
        return view('client_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->m) {
            return view('login');
        }
        
        $request->validate([
            'email' => 'email|unique:clients',
            'cnpj_cpf' => 'unique:clients'
        ], [
            'email.email' => 'Digite um e-mail válido',
            'email.unique' => 'O e-mail digitado está em uso, por favor escolha outro.',
            'cnpj_cpf.unique' => 'O CNPJ digitado está já foi cadastrado.',
        ]);

        $created = $this->client->create([
            'name' => $request->input('name'),
            'cnpj_cpf' => $request->input('cnpj_cpf'),
            'cep' => $request->input('cep'),
            'unit' => $request->input('unit'),
            'address' => $request->input('address'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'contact' => $request->input('contact'),
        ]);
        if ($created) {
            return redirect()->route('clients.index')->with('message', 'Cliente cadastrado com sucesso.');
        }
        return redirect()->route('clients.index')->with('message', 'Erro ao cadastrar cliente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        if (!$this->m) {
            return view('login');
        }
        
        return view('client_delete', ['client' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        if (!$this->m) {
            return view('login');
        }
        
        return view('client_edit', ['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$this->m) {
            return view('login');
        }
        
        $updated = $this->client->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->back()->with('message', 'Erro ao atualizar cadastro.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->m) {
            return view('login');
        }
        
        $deleted = $this->client->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('clients.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('clients.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
