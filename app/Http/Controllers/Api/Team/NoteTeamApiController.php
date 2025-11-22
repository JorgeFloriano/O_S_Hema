<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormOrderApiRequest;
use App\Models\Cause;
use App\Models\Defect;
use App\Models\Material;
use App\Models\NoteType;
use App\Models\Order;
use App\Models\Solution;
use App\Models\Tec;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteTeamApiController extends Controller
{
    public $can;

    public function __construct()
    {
        $this->can = new ResponseJson();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get orders with relationships
        // The user data (id, name, surname) is now automatically loaded
        //$order->tec->user will contain only id, name, surname

        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            return $this->can->AuthIsTec();
        }

        $orders = Order::with([
            'type:id,description',
            'client:id,name',
            'tec:id,user_id',
            'tec.user:id,name,surname',
        ])
            ->where('tec_id', Auth::user()->tec->id)
            ->orderBy('id', 'desc')
            ->get(['id', 'order_type_id', 'client_id', 'tec_id', 'req_descr', 'req_name', 'sector', 'req_date', 'req_time', 'finished']);

        return response()->json(['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Order $order): JsonResponse
    {
        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            return $this->can->AuthIsTec();
        }

        // Load order with relationships
        $order = $order->load([
            'type:id,description',
            'client:id,name,unit,address,contact',
            'tec:id,user_id'
        ]);

        // Get all necessary data
        $tecs = Tec::with('user:id,name,surname')->get();
        $types = NoteType::select('id', 'description')->orderBy('description')->get();
        $defects = Defect::select('id', 'description')->orderBy('description')->get();
        $causes = Cause::select('id', 'description')->orderBy('description')->get();
        $solutions = Solution::select('id', 'description')->orderBy('description')->get();
        $materials = Material::select('id', 'description')->orderBy('description')->get();

        return response()->json([
            'success' => true,
            'order' => $order,
            'tecs' => $tecs,
            'types' => $types,
            'defects' => $defects,
            'causes' => $causes,
            'solutions' => $solutions,
            'materials' => $materials
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormOrderApiRequest $request)
    {
        $auth = Auth::user();

        $return_error = $this->can->error([$auth->cli->can_create_sat], 'Usuário sem permissão para salvar ordens.');

        if ($return_error) {
            return $return_error;
        }

        $client_id = $auth->cli->client_id;
        $complete_name = $auth->name . ' ' . $auth->surname;
        $user_id = $auth->id;


        try {
            $text = new TextFormat;

            // Create new order
            $order = Order::create([
                'client_id' => $client_id,
                'order_type_id' => $request->order_type_id,
                'sector' => $request->sector,
                'req_name' => $complete_name, // Fixed variable name
                'user_id' => $user_id, // Use auth()->id() instead of auth()->user()->id
                'tec_id' => null,
                'equipment' => $request->equipment,
                'req_date' => now()->format('Y-m-d'), // Current date in proper format
                'req_time' => now()->format('H:i:s'), // Current time in proper format
                'req_descr' => $text->spaceAfterPunctuation($request->req_descr),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de Assistência Técnica criada com sucesso.',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
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
    public function show($id)
    {

        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            return $this->can->AuthIsTec();
        }

        $order = Order::with(['type:id,description', 'client:id,name', 'tec:id,user_id', 'notes.materials', 'notes.tecs.user:id,name,surname,function'])
            ->select('id', 'client_id', 'equipment', 'finished', 'order_type_id', 'req_date', 'req_descr', 'req_name', 'req_time', 'sector', 'tec_id', 'user_id')
            ->find($id);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
