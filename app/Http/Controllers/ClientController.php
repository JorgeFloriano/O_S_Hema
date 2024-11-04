<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCliRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

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
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }

        $clients = $this->client->select('id', 'name','unit')->simplePaginate(20);

        return view('client.clients_list' , ['clients' => $clients]);
    }

    public function create()
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }
        
        return view('client.client_create');
    }

    public function store(FormCliRequest $request)
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }
        
        $request->validated();

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

    public function show($client)
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }

        // Decrypt the client id
        try {
            $client = $this->client->find(Crypt::decryptString($client));
        } catch (DecryptException $e) {
            return redirect()->back()->withErrors(['error' => 'Falha de desencriptação.']);
        }
        
        return view('client.client_delete', ['client' => $client]);
    }

    public function edit($client)
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }

        // Decrypt the client id
        try {
            $client = $this->client->find(Crypt::decryptString($client));
        } catch (DecryptException $e) {
            return redirect()->back()->withErrors(['error' => 'Falha de desencriptação.']);
        }
        
        return view('client.client_edit', ['client' => $client]);
    }

    public function update(FormCliRequest $request, string $id)
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }

        $request->validated();
        
        $updated = $this->client->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->back()->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        // If the user isn't main and isn't client, redirect to login page
        if (!$this->m && session('cli') !== auth()->user()->id) {
            return view('login');
        }
        
        $deleted = $this->client->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('clients.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('clients.index')->with('message', 'Erro ao deletar cadastro.');
    }
}
