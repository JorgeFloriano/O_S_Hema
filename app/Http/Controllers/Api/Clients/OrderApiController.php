<?php

namespace App\Http\Controllers\Api\Clients;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormOrderApiRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    private $resp_json;
    public readonly User $auth;

    public function __construct()
    {
        $this->resp_json = new ResponseJson();
        $this->auth = Auth::user();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get orders with relationships
        // The user data (id, name, surname) is now automatically loaded
        //$order->tec->user will contain only id, name, surname

        if (!$this->auth) {
            logger_main('error', 'Usuário sem cadastro.');
            return $this->resp_json->array(false, 'Usuário sem cadastro.', 403);
        }

        $client_id = $this->auth->cli->client_id;

        $orders = Order::with([
            'type:id,description',
            'tec:id,user_id',
            'tec.user:id,name,surname',
        ])
            ->where('client_id', $client_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get(['id', 'order_type_id', 'tec_id', 'req_descr', 'req_name', 'sector', 'req_date', 'req_time', 'finished']);

        return response()->json(['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->auth->canCreateSat()) {
            logger_main('error', 'Usuário sem permissão para criar SAT.');
            return $this->resp_json->array(false, 'Usuário sem permissão para criar SAT.', 403);
        }

        // Get all order types
        $types = OrderType::select('id', 'description')->get();

        return response()->json([
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormOrderApiRequest $request)
    {
        if (!$this->auth->canCreateSat()) {
            logger_main('error', 'Usuário sem permissão para salvar SAT.');
            return $this->resp_json->array(false, 'Usuário sem permissão para salvar SAT.', 403);
        }

        // Check if 'client_id' is fillable ou SAT was created by specific user client
        if (!$this->auth->userClientCompanyId() && !$request->client_id) {
            logger_main('error', 'ID do cliente não encontrado');
            return $this->resp_json->array(false, 'ID do cliente não encontrado', 400);
        }

        // Get client_id
        $client_id = $this->auth->userClientCompanyId() ? $this->auth->userClientCompanyId() : $request->client_id;

        try {
            $text = new TextFormat;

            // Create new order
            $order = Order::create([
                'client_id' => $client_id,
                'order_type_id' => $request->order_type_id,
                'sector' => $request->sector,
                'req_name' => $this->auth->getFullName(), // Fixed variable name
                'user_id' => $this->auth->id, // Use auth()->id() instead of auth()->user()->id
                'tec_id' => null,
                'equipment' => $request->equipment,
                'is_emergency' => $request->is_emergency ? true : false,
                'req_date' => now()->format('Y-m-d'), // Current date in proper format
                'req_time' => now()->format('H:i:s'), // Current time in proper format
                'req_descr' => $text->spaceAfterPunctuation($request->req_descr),
            ]);

            // Notification management when a Technical Assistance Request is opened by the client.
            $order->notificationWhenOpenedByClient();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de Assistência Técnica criada com sucesso.',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            logger_main('error', 'Erro ao criar Solicitação de Assistência Técnica.' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar Solicitação de Assistência Técnica.' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if (!$this->auth->canSeeSat()) {
            logger_main('error', 'Usuário sem permissão para ver SATs.');
            return $this->resp_json->array(false, 'Usuário sem permissão para ver SATs.', 403);
        }

        $order = $order->load(['type:id,description', 'tec:id,user_id', 'notes.materials', 'notes.tecs.user:id,name,surname,function']);
        return response()->json([
            'order' => $order
        ]);
    }
}
