<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCliRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public readonly Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function index()
    {
        Gate::authorize('check-permission', ['clients', 1]);

        return redirect()->route('clients.list' , 1);
    }

    public function list(bool $opt)
    {
        Gate::authorize('check-permission', ['clients', 1]);

        if ($opt == 0) {
            $clients = $this->client->select('id', 'name','unit')->orderBy('name')->onlyTrashed()->simplePaginate(20);
            $opt = 1;
            $icon = 'undo';
            $msg = 'Desativados';
            $cond = 'Reativar';
            $title = 'Ativos';
            $route = 'clients.restore';
        } else {
            $clients = $this->client->select('id', 'name','unit')->orderBy('name')->simplePaginate(20);
            $opt = 0;
            $icon = 'archive';
            $msg = 'Cadastrados';
            $cond = 'Arquivar';
            $title = 'Arquivados';
            $route = 'clients.desativate';
        }

        return view('client.clients_list', [
            'clients' => $clients,
            'opt' => $opt,
            'msg' => $msg,
            'cond' => $cond,
            'title' => $title,
            'icon' => $icon,
            'route' => $route
        ]);
    }

    public function create()
    {
        Gate::authorize('check-permission', ['clients', 2]);
        
        return view('client.client_create');
    }

    public function store(FormCliRequest $request)
    {
        Gate::authorize('check-permission', ['clients', 2]);
        
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
        Gate::authorize('check-permission', ['clients', 1]);

        // Decrypt the client id
        try {
            $client = $this->client->withTrashed()->find(Crypt::decryptString($client));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }
        
        return view('client.client_delete', ['client' => $client]);
    }

    public function edit($client)
    {
        Gate::authorize('is-main-adm');

        // Decrypt the client id
        try {
            $client = $this->client->find(Crypt::decryptString($client));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }
        
        return view('client.client_edit', ['client' => $client]);
    }

    public function update(FormCliRequest $request, string $id)
    {
        Gate::authorize('is-main-adm');

        $request->validated();
        
        $updated = $this->client->where('id', $id)->update($request->except(['_token', '_method']));

        if ($updated) {
            return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
        }
        return redirect()->back()->with('message', 'Erro ao atualizar cadastro.');
    }

    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['clients', 2]);
        
        $deleted = $this->client->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('clients.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('clients.index')->with('message', 'Erro ao deletar cadastro.');
    }

    public function restore(string $id)
    {
        Gate::authorize('check-permission', ['clients', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $restored = $this->client->where('id', $id)->restore();

        if ($restored) {
            return redirect()->route('clients.list', 0)->with('message', 'Cadastro restaurado com sucesso.');
        }
        return redirect()->route('clients.list', 0)->with('message', 'Erro ao restaurar cadastro.');
    }

    public function desativate(string $id)
    {
        Gate::authorize('check-permission', ['clients', 2]);

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }
        
        $deleted = $this->client->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('clients.list', 1)->with('message', 'Cadastro desativado com sucesso.');
        }
        return redirect()->route('clients.list', 1)->with('message', 'Erro ao desativar cadastro.');
    }
}
