<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public readonly Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function index()
    {
        $clients = $this->client->select('id', 'name','unit')->get();

        return view('clients_list' , ['clients' => $clients]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return view('client_delete', ['client' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('client_edit', ['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
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
        $deleted = $this->client->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('clients.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('clients.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
