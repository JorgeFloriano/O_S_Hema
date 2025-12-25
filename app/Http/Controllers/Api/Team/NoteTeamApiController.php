<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Class\Signature;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormApiNoteRequest;
use App\Models\Cause;
use App\Models\Defect;
use App\Models\Material;
use App\Models\Note;
use App\Models\NoteType;
use App\Models\Order;
use App\Models\Solution;
use App\Models\Tec;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteTeamApiController extends Controller
{
    public $can;
    public $text;

    public function __construct()
    {
        $this->can = new ResponseJson();

        // Class with text format functions
        $this->text = new TextFormat;
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

        $tec = Auth::user()->tec;

        $orders = Order::with([
            'type:id,description',
            'client:id,name',
            'tec:id,user_id',
            'tec.user:id,name,surname',
        ])
            ->where('tec_id', $tec->id)
            ->orderBy('id', 'desc')
            ->get(['id', 'order_type_id', 'client_id', 'tec_id', 'req_descr', 'req_name', 'sector', 'req_date', 'req_time', 'finished']);

        return response()->json([
            'orders' => $orders,
            'emergency_order_id' => $tec->emergency_order_id
        ]);
    }

    public function clearEmergency(Request $request)
    {
        $tec = Auth::user()->tec;

        // Se a ordem que ele abriu for a de emergência, limpamos o ID
        if ($tec->emergency_order_id == $request->order_id) {
            $tec->update(['emergency_notification_pending' => false]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id): JsonResponse
    {
        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            return $this->can->AuthIsTec();
        }

        // Load order with relationships
        // $order = $order->load([
        //     'type:id,description',
        //     'client:id,name,unit,address,contact',
        //     'tec:id,user_id',
        //     'notes'
        // ]);

        $order = Order::with([
            'type:id,description',
            'client:id,name,unit,address,contact',
            'tec:id,user_id',
            'user:id,name,surname',
            'notes',
        ])->select('id', 'client_id', 'equipment', 'req_date', 'req_descr', 'req_name', 'req_time', 'sector', 'user_id')
            ->find($id);

        // Notes relation is loaded, only check if order has notes
        $order->hasNotes = $order->notes->isNotEmpty();

        // Get all necessary data
        $tecs = Tec::with('user:id,name,surname')->get();
        $types = NoteType::select('id', 'description')->orderBy('description')->get();
        $defects = Defect::select('id', 'description')->orderBy('description')->get();
        $causes = Cause::select('id', 'description')->orderBy('description')->get();
        $solutions = Solution::select('id', 'description')->orderBy('description')->get();
        $materials = Material::select('id', 'description', 'unit')->orderBy('description')->get();

        return response()->json([
            'success' => true,
            'order' => $order,
            'tecs' => $tecs,
            'types' => $types,
            'defects' => $defects,
            'causes' => $causes,
            'solutions' => $solutions,
            'materials' => $materials,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormApiNoteRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $validated = $request->validated();

            $signature = new Signature();

            // Process signatures
            $signTec1Path = $signature->compress($request->sign_t_1, 'tec1');
            $signTec2Path = $request->filled('sign_t_2') ? $signature->compress($request->sign_t_2, 'tec2') : null;
            $signClientPath = $request->filled('sign_cl') ? $signature->compress($request->sign_cl, 'client') : null;

            // Process services text
            $services = $this->text->spaceAfterPunctuation($validated['services']);

            // Prevent same technician
            $second_tec = $request->first_tec == $request->second_tec ? null : $request->second_tec;

            // Create note
            $note = Note::create([
                'order_id' => $validated['order_id'],
                'equip_mod' => $validated['equip_mod'],
                'equip_id' => $validated['equip_id'],
                'equip_type' => $validated['equip_type'],
                'note_type_id' => $validated['note_type_id'],
                'defect_id' => $validated['defect_id'],
                'cause_id' => $validated['cause_id'],
                'solution_id' => $validated['solution_id'],
                'services' => $services,
                'date' => $validated['date'], // Already converted in FormRequest
                'go_start' => $validated['go_start'] ?? null,
                'go_end' => $validated['go_end'] ?? null,
                'start' => $validated['start'],
                'end' => $validated['end'],
                'back_start' => $validated['back_start'] ?? null,
                'back_end' => $validated['back_end'] ?? null,
                'km_start' => $validated['km_start'] ?? null,
                'km_end' => $validated['km_end'] ?? null,
            ]);

            // Attach materials
            if ($request->has('materials') && is_array($request->materials)) {
                $materialsData = [];

                foreach ($request->materials as $material) {
                    if ($material['quantity'] > 0) {
                        $materialsData[$material['material_id']] = [
                            'quantity' => $material['quantity']
                        ];
                    }
                }

                $note->materials()->sync($materialsData);
            }

            // Prepare technicians data
            $technicians = [
                [
                    'tec_id' => $validated['first_tec'],
                    'signature_path' => $signTec1Path,
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];

            if ($request->filled('second_tec')) {
                $technicians[] = [
                    'tec_id' => $validated['second_tec'],
                    'signature_path' => $signTec2Path,
                    'is_primary' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Attach technicians
            $note->tecs()->sync($technicians);

            // Update order
            $order = Order::findOrFail($validated['order_id']);

            $order->update([
                'cl_name' => $validated['cl_name'] ?? null,
                'cl_function' => $validated['cl_function'] ?? null,
                'cl_contact' => $validated['cl_contact'] ?? null,
                'cl_date' => now()->format('Y-m-d'),
                'cl_sign_path' => $signClientPath,
                'finished' => $validated['finished'],
            ]);

            if ($order->finished) {
                $order->finish();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso!',
                'note' => $note->load(['materials', 'tecs']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating note', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['sign_t_1', 'sign_t_2', 'sign_cl']), // Exclude signature data from logs
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento. Tente novamente.',
                'error' => config('app.debug') ? $e->getMessage() : null,
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
