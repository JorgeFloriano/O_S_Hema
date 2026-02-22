<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\ResponseJson;
use App\Models\Tec;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmergencyApiController extends Controller
{
    private $can;
    private readonly User $auth;
    public function __construct()
    {
        $this->can = new ResponseJson();
        $this->auth = Auth::user();
    }

    // Listar todos os técnicos com seus usuários e contagem de clientes
    public function index()
    {
        // Check if user is a supervisor that has 'manager_on_call' permission
        if (!$this->auth->hasPermission('manager_on_call')) {
            logger_main('error', 'Acesso Negado: Usuário sem permissão para gerenciar sobreaviso.');
            return $this->can->array(false, 'Usuário sem permissão para gerenciar sobreaviso.', 403);
        }

        $tecs = Tec::with([
            'user',
            'emergencyClients:id', // Carrega apenas o ID dos clientes
            'emergencyOrder:id,finished,tec_id' // Colunas necessárias para a lógica de 'busy'
        ])
            ->join('users', 'tecs.user_id', '=', 'users.id')
            ->whereNotIn('tecs.user_id', [1, 2, 9999, 0])
            ->orderBy('users.name')
            ->select('tecs.*')
            ->get(); // Se preferir paginação no mobile, troque por paginate(20)

        // Aplicando a lógica de negócio no conjunto de dados
        $tecs->transform(function ($tec) {
            $order = $tec->emergencyOrder;

            // Adiciona o atributo dinâmico 'busy' (ocupado)
            // Usado no mobile para mostrar "Disponível" ou "Ocupado na SAT #"
            $tec->busy = ($order && !$order->finished && $order->tec_id == $tec->id);

            // Opcional: Se quiser enviar o ID da SAT diretamente para facilitar o link no mobile
            $tec->emergency_order_id = ($tec->busy) ? $order->id : null;

            return $tec;
        });

        return response()->json($tecs);
    }

    // Listar todos os clientes para o Modal de Seleção
    public function getClients()
    {
        // Check if user is a supervisor
        if ($this->can->AuthIsSup()) {
            logger_main('error', 'Acesso Negado: Usuário sem permissão para gerenciar clientes.');
            return $this->can->AuthIsSup();
        }

        return response()->json(Client::select('id', 'name')->orderBy('name')->get());
    }

    // Ativar/Desativar o sobreaviso (O Switch do Card)
    public function toggleActive(Request $request, $id)
    {
        // Check if user is a supervisor that has 'manager_on_call' permission
        if (!$this->auth->hasPermission('manager_on_call')) {
            logger_main('error', 'Acesso Negado: Usuário sem permissão para gerenciar condição de sobreaviso dos técnicos.');
            return $this->can->array(false, 'Usuário sem permissão para gerenciar condição de sobreaviso dos técnicos.', 403);
        }

        $tec = Tec::findOrFail($id);
        $tec->update([
            'on_call' => $request->on_call ? 1 : 0
        ]);

        return response()->json(['message' => 'Status atualizado com sucesso']);
    }

    // Sincronizar Clientes (O "Salvar Todos" do Modal)
    public function syncClients(Request $request, $id)
    {
        // Check if user is a supervisor that has 'manager_on_call' permission
        if (!$this->auth->hasPermission('manager_on_call')) {
            logger_main('error', 'Acesso Negado: Usuário sem permissão para atribuir clientes aos técnicos de sobreaviso.');
            return $this->can->array(false, 'Usuário sem permissão para atribuir clientes aos técnicos de sobreaviso.', 403);
        }

        $request->validate([
            'clients' => 'array' // Garante que recebemos um array de IDs [1, 2, 3]
        ]);

        $tec = Tec::findOrFail($id);

        // O método sync remove os que não estão no array e adiciona os novos
        $tec->emergencyClients()->sync($request->clients);

        return response()->json([
            'message' => 'Clientes vinculados com sucesso',
            'tec' => $tec->load('emergencyClients')
        ]);
    }
}
