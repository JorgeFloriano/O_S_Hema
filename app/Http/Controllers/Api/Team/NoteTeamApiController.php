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
use App\Services\FileService;
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
            logger_main('error', 'Usuário sem permissão para acessar anotações em sats.');
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
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->orderBy('id', 'desc')
            ->get(['id', 'order_type_id', 'client_id', 'tec_id', 'req_descr', 'req_name', 'sector', 'req_date', 'req_time', 'equipment', 'finished', 'is_emergency']);

        return response()->json([
            'orders' => $orders,
            'emergency_order_id' => $tec->emergency_order_id
        ]);
    }

    // Para as notificações de emergência e atribui a SAT ao primeiro técnico que abriu a SAT
    public function clearEmergency(Request $request)
    {
        $user = Auth::user();
        $currentTec = $user->tec;
        $orderId = $request->order_id;

        // 1. Verificamos se a SAT existe
        $order = Order::find($orderId);
        if (!$order) {
            logger_main('error', 'SAT nao encontrada');
            return response()->json(['error' => 'SAT não encontrada'], 404);
        }

        // 2. TRAVA DE SEGURANÇA: 
        // Se a SAT já tem um técnico e não é o logado, ele não pode assumir/limpar
        if ($order->tec_id && $order->tec_id != $currentTec->id) {
            logger_main('error', 'SAT ja atribuida a outro técnico');
            return response()->json([
                'error' => 'SAT já atribuída a outro técnico.',
                'already_taken' => true
            ], 403); // 403 Forbidden
        }

        // 3. Verificamos se o usuário logado é tecnico
        if ($this->can->AuthIsTec()) {
            return $this->can->AuthIsTec();
        }

        // 4. Verifica se  ha algum loop de notificações pendentes para essa SAT
        $notificationsPending = Tec::where('emergency_order_id', $orderId)
            ->where('emergency_notification_pending', true)
            ->exists();
        if (!$notificationsPending) {
            return response()->json(['success' => true, 'mode' => 'standard']);
        }

        // 5. PARAR NOTIFICAÇÕES PARA TODOS:
        // Limpa a flag de loop (pending) de TODOS os técnicos que estavam com essa SAT aberta
        Tec::where('emergency_order_id', $orderId)
            ->update(['emergency_notification_pending' => false]);

        // 6. LIMPAR VISUAL DOS OUTROS:
        // Remove o ID da emergência de todos, EXCETO do técnico logado
        // Isso faz com que o ícone vermelho suma para os outros, pois alguém já assumiu.
        Tec::where('emergency_order_id', $orderId)
            ->where('id', '!=', $currentTec->id)
            ->update(['emergency_order_id' => null]);

        // 7. ASSUMIR A SAT:
        // Vincula formalmente a SAT ao técnico que a abriu primeiro
        $tec_seen_sat = $order->update(['tec_id' => $currentTec->id]);

        // 8. NOTIFICAÇÃO DE QUE A SAT FOI ASSUMIDA:
        // Envia uma notificação para os supervisores que a SAT foi assumida
        if ($tec_seen_sat) {
            Log::info("Send Emergency Supervisor Notification: Técnico #{$currentTec->id} assumiu a SAT #{$orderId}");
            $order->notifySupsThatTecGetEmergencySat($currentTec->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Você assumiu a SAT e os alertas foram interrompidos para a equipe.'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id): JsonResponse
    {
        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            logger_main('error', 'Usuário sem cadastro de técnico.');
            return $this->can->AuthIsTec();
        }

        $order = Order::with([
            'type:id,description',
            'client:id,name,unit,address,contact',
            'tec:id,user_id',
            'user:id,name,surname',
            'notes',
        ])->select('id', 'client_id', 'equipment', 'req_date', 'req_descr', 'req_name', 'req_time', 'sector', 'user_id')
            ->find($id);

        // Se a SAT já foi atribuida a outro técnico, bloqueia a tela do formulário
        $tec = Auth::user()->tec;
        if (!is_null($order->tec_id) && $order->tec_id != $tec->id) {
            logger_main('error', 'Acesso negado. Esta SAT está vinculada a outro técnico.');
            return response()->json(['error' => 'Acesso negado. Esta SAT está vinculada a outro técnico.'], 403);
        }

        // Notes relation is loaded, only check if order has notes
        $order->hasNotes = $order->notes->isNotEmpty();

        // Get all necessary data
        $tecs = Tec::with('user:id,name,surname')->get();
        $types = NoteType::select('id', 'description')->orderBy('description')->get();
        $defects = Defect::select('id', 'description')->orderBy('description')->get();
        $causes = Cause::select('id', 'description')->orderBy('description')->get();
        $solutions = Solution::select('id', 'description')->orderBy('description')->get();
        $materials = Material::select('id', 'description', 'code', 'unit')->orderBy('description')->get();

        foreach ($materials as $key => $material) {
            $material->description = $material->completeDescription();
        }

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
    public function store(FormApiNoteRequest $request, FileService $fileService): JsonResponse
    {
        // Check if user is a technician
        if ($this->can->AuthIsTec()) {
            logger_main('error', 'Usuário sem cadastro de técnico.');
            return $this->can->AuthIsTec();
        }

        // Start transaction
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // SAT / Order
            $order = Order::findOrFail($validated['order_id']);

            // Não pode salvar anotação na SAT / Order de outro técnico
            $tec = Auth::user()->tec;
            if (!is_null($order->tec_id) && $order->tec_id != $tec->id) {
                logger_main('error', 'Acesso negado. Esta SAT está vinculada a outro técnico.');
                return response()->json(['error' => 'Acesso negado. Esta SAT está vinculada a outro técnico.'], 403);
            }

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

            if (!$note) {
                DB::rollBack();
                logger_main('error', 'Erro ao criar anotação.');
                return response()->json(['error' => 'Erro ao criar anotação.'], 500);
            }

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

                $sync_materials = $note->materials()->sync($materialsData);

                if (!$sync_materials) {
                    DB::rollBack();
                    logger_main('error', 'Erro ao salvar materiais.');
                    return response()->json(['error' => 'Erro ao salvar materiais.'], 500);
                }
            }

            if ($request->has('files')) {
                // O Laravel trata múltiplos arquivos enviados com o mesmo nome como um array
                $files = $fileService->storeMultipleFiles($note, $request->file('files'), 'notes');

                if (!$files) {
                    DB::rollBack();
                    logger_main('error', 'Erro ao salvar arquivos.');
                    return response()->json(['error' => 'Erro ao salvar arquivos.'], 500);
                }
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
            $note_tecs = $note->tecs()->sync($technicians);

            if (!$note_tecs) {
                DB::rollBack();
                logger_main('error', 'Erro ao vincular técnicos.');
                return response()->json(['error' => 'Erro ao vincular técnicos.'], 500);
            }

            $order_updated = $order->update([
                'cl_name' => $validated['cl_name'] ?? null,
                'cl_function' => $validated['cl_function'] ?? null,
                'cl_contact' => $validated['cl_contact'] ?? null,
                'cl_date' => now()->format('Y-m-d'),
                'cl_sign_path' => $signClientPath,
                'finished' => $validated['finished'],
            ]);

            if (!$order_updated) {
                DB::rollBack();
                logger_main('error', 'Erro ao atualizar SAT.');
                return response()->json(['error' => 'Erro ao atualizar SAT.'], 500);
            }

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

            logger_main('error', 'Erro ao registrar atendimento. Tente novamente.');
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
        if ($this->can->isAuth()) {
            logger_main('error', 'Usuário sem permissão para acessar anotações em sats.');
            return $this->can->isAuth();
        }

        $order = Order::with(['type:id,description', 'client:id,name', 'tec:id,user_id', 'notes.materials', 'notes.tecs.user:id,name,surname,function'])
            ->select('id', 'client_id', 'equipment', 'finished', 'order_type_id', 'req_date', 'req_descr', 'req_name', 'req_time', 'sector', 'tec_id', 'user_id')
            ->find($id);

        return response()->json([
            'order' => $order
        ]);
    }
}
