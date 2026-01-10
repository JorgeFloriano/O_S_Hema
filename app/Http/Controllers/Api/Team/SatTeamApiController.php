<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SatTeamApiController extends Controller
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

        // Check if user is a supervisor
        if ($this->can->AuthIsSup()) {
            return $this->can->AuthIsSup();
        }

        $orders = Order::with([
            'type:id,description',
            'client:id,name',
            'tec:id,user_id',
            'tec.user:id,name,surname',
        ])
            ->where('created_at', '>', now()->subDays(30))
            ->orderBy('id', 'desc')
            ->get(['id', 'order_type_id', 'client_id', 'tec_id', 'req_descr', 'req_name', 'sector', 'req_date', 'req_time', 'equipment', 'finished']);

        return response()->json([
            'orders' => $orders,
        ]);
    }
    public function update_tec(Request $request, $id)
    {

        // Check if user is a supervisor
        if ($this->can->AuthIsSup()) {
            return $this->can->AuthIsSup();
        }

        $tec = Tec::findOrFail($request->tec_id);

        // Update the order
        $order = Order::findOrFail($id);
        $order->update(['tec_id' => $request->tec_id]);


        Tec::where('emergency_order_id', $order->id)->update([
            'emergency_order_id' => null,
            'emergency_notification_pending' => false
        ]);

        // Enviamos uma notificação para o técnico
        if ($notifiable = User::find($tec->user_id)) {
            try {
                $order->satNotification($notifiable);
                return response()->json([
                    'success' => true,
                    'message' => 'SAT atribuida e técnico notificado com sucesso!'
                ]);
            } catch (\Exception $e) {
                Log::error("Falha ao notificar técnico {$notifiable->name} para SAT #{$order->id}: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'SAT atribuida, não foi possivel notificar o técnico!'
                ]);
            }
        }

        return response()->json([
            Log::error("Falha ao notificar técnico para SAT #{$order->id}, usuário desconhecido."),
            'success' => false,
            'message' => 'SAT atribuida, não foi encontrado usuário!'
        ]);
    }
}
