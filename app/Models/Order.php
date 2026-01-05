<?php

namespace App\Models;

use App\Class\Hours;
use App\Notifications\NewSampleNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_type_id',
        'client_id',
        'sector',
        'tec_id',
        'user_id',
        'finished',
        'equipment',
        'req_name',
        'req_date',
        'req_time',
        'req_descr',
        'cl_name',
        'cl_function',
        'cl_contact',
        'cl_date',
        'cl_sign',
        'cl_sign_path'
    ];

    protected $table = "orders";
    protected $primaryKey = "id";

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function tec(): BelongsTo
    {
        return $this->belongsTo(Tec::class)->withTrashed();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type_id')->withTrashed();
    }

    // Null values will be replaced by - - : - - and the time will be formatted without seconds
    public function notes_time_format()
    {
        foreach ($this->notes as $note) {
            $note->go_start ? $note->go_start = date('H:i', strtotime($note->go_start)) : $note->go_start = ' - - : - -';
            $note->go_end ? $note->go_end = date('H:i', strtotime($note->go_end)) : $note->go_end = ' - - : - -';
            $note->start ? $note->start = date('H:i', strtotime($note->start)) : $note->start = ' - - : - -';
            $note->end ? $note->end = date('H:i', strtotime($note->end)) : $note->end = ' - - : - -';
            $note->back_start ? $note->back_start = date('H:i', strtotime($note->back_start)) : $note->back_start = ' - - : - -';
            $note->back_end ? $note->back_end = date('H:i', strtotime($note->back_end)) : $note->back_end = ' - - : - -';
        }
    }

    // Order function that Starts the emergency notifications
    public function startEmergencyNotifications()
    {
        // Get the order created
        $order = Order::with('client:id,name')->find($this->id);

        // Verificação de Horário de Emergência
        $hours = new Hours();
        if ($hours->isEmergency()) {
            // Buscamos todos os técnicos que estão de plantão, vinculados a este cliente e que ainda não tem uma ordem de emergência atribuida
            $client = Client::with(['emergencyTecs' => function ($query) {
                $query->with('user')
                    ->where('on_call', 1)
                    ->where(function ($q) {
                        $q->whereNull('emergency_order_id')
                            ->orWhere('emergency_order_id', 0)
                            ->orWhere('emergency_order_id', '');
                    });
            }])->find($order->client_id);

            foreach ($client->emergencyTecs as $tec) {
                // Atualizamos cada técnico para o estado de emergência
                $tec->update([
                    'emergency_order_id' => $order->id,
                    'emergency_notification_pending' => true, // Loop notification activated
                ]);

                // Enviamos uma notificação para cada técnico em 30 segundos
                \App\Jobs\EmergencySatNotifications::dispatch($tec->id, $order->id); // Send notification to technician());
            }
        }
    }

    public function finish(): bool
    {
        try {
            // 1. Limpa o estado de emergência dos técnicos
            Tec::where('emergency_order_id', $this->id)->update([
                'emergency_order_id' => null,
                'emergency_notification_pending' => false
            ]);

            // 2. Finaliza a ordem
            $this->finished = true;
            $order_finished = $this->save();

            // 3. CORREÇÃO AQUI: Busca o usuário através do Model Tec
            $tec = Tec::with('user')->find($this->tec_id);
            $order_tec_name = ($tec && $tec->user) ? $tec->user->name : 'Técnico Desconhecido';

            // 4. Notifica os supervisores
            $supervisors = User::whereHas('sup')->get();

            foreach ($supervisors as $sup) {
                $sup->title = "SAT {$this->id} - " . $this->client->name . " - finalizada por {$order_tec_name}!";
                $sup->order_id = $this->id;
                $sup->type = 'sat_finished';
                $sup->message = $this->req_descr ?? 'Atendimento concluído.';

                $sup->notify(new NewSampleNotification());
            }

            return $order_finished;
        } catch (\Exception $e) {
            Log::error("Erro ao finalizar SAT #{$this->id}: " . $e->getMessage());
            return false;
        }
    }
}
