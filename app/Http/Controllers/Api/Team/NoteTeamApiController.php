<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormOrderApiRequest;
use App\Models\Cause;
use App\Models\Defect;
use App\Models\Material;
use App\Models\Note;
use App\Models\NoteTec;
use App\Models\NoteType;
use App\Models\Order;
use App\Models\Solution;
use App\Models\Tec;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'tec:id,user_id',
            'notes'
        ]);

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
            'order' => $order->withoutRelations('notes'),
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
    public function store(Request $request)
    {

        $validated = $request->validate([
            // ... your existing validation rules
            'sign_t_1' => 'required|string',
            'sign_t_2' => 'nullable|string',
            'sign_cl' => 'nullable|string',
        ]);

        // Process signatures - store as files instead of base64 in database
        $signTec1Path = $this->storeSignatureAsFile($request->sign_t_1, 'tec1');
        $signTec2Path = $request->has('sign_t_2') ? $this->storeSignatureAsFile($request->sign_t_2, 'tec2') : null;
        $signClientPath = $request->has('sign_client') ? $this->storeSignatureAsFile($request->sign_client, 'client') : null;

        $note = Note::create([
            'order_id' => $request->order_id,
            'equip_mod' => $request->equip_mod,
            'equip_id' => $request->equip_id,
            'equip_type' => $request->equip_type,
            'note_type_id' => $request->note_type_id,
            'defect_id' => $request->defect_id,
            'cause_id' => $request->cause_id,
            'solution_id' => $request->solution_id,
            'services' => $this->text->spaceAfterPunctuation($request->services),
            'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
            'go_start' => $request->go_start,
            'go_end' => $request->go_end,
            'start' => $request->start,
            'end' => $request->end,
            'back_start' => $request->back_start,
            'back_end' => $request->back_end,
            'km_start' => $request->km_start,
            'km_end' => $request->km_end,
        ]);

        // Create note_tec for first_tec
        if ($note) {
            $signature = NoteTec::create([
                'note_id' => $note->id,
                'tec_id' => $request->input('first_tec'),
                'signature_path' => $signTec1Path,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Atendimento registrado com sucesso!',
            'note' => $note
        ]);
    }

    private function storeSignatureAsFile($base64Image, $prefix)
    {
        try {
            // Remove the data:image/png;base64, part
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            $image = str_replace(' ', '+', $image);

            // Decode base64
            $imageData = base64_decode($image);

            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            // Generate unique filename
            $filename = $prefix . '_' . uniqid() . '_' . time() . '.png';
            $directory = 'signatures/' . date('Y/m');
            $fullPath = $directory . '/' . $filename;

            // Ensure directory exists using Storage facade
            Storage::disk('public')->makeDirectory($directory);

            // Store file
            Storage::disk('public')->put($fullPath, $imageData);

            return $fullPath;
        } catch (\Exception $e) {
            Log::error('Error storing signature: ' . $e->getMessage());
            // Fallback: you could store the original base64 if file storage fails
            return null;
        }
    }
    

     // fix the material store ------------------------------------------------------
        // $validated = $request->validate([
        //     // ... your existing validation
        //     'materials' => 'sometimes|array',
        //     'materials.*.material_id' => 'required|exists:materials,id',
        //     'materials.*.quantity' => 'required|numeric|min:0',
        // ]);

        // // Create the note
        // $note = Note::create($request->except('materials'));

        // // Attach materials with quantities
        // if ($request->has('materials')) {
        //     foreach ($request->materials as $material) {
        //         $note->materials()->attach($material['material_id'], [
        //             'quantity' => $material['quantity']
        //         ]);
        //     }
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Atendimento registrado com sucesso!',
        //     'note' => $note
        // ]);

        //--------------------------------------------------------------------------

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
