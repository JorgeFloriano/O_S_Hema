<?php

namespace App\Models;

use App\Class\Hours;
use App\Notifications\NewSampleNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    // Notification management when a Technical Assistance Request is opened by the client.
    public function notificationWhenOpenedByClient()
    {
        // Get the order created
        $order = Order::with('client:id,name')->find($this->id);

        // Get all supervisors
        $supervisors = User::whereHas('sup')->get();

        // Verificação de Horário de Emergência
        $hours = new Hours();

        // Notifica os supervisores sobre a SAT
        foreach ($supervisors as $sup) {
            $order->emergencySatNotification($sup);
        }

        // Horário de comercial, não é emergência
        if (!$hours->isEmergency()) {
            return;
        }

        // Buscamos todos os técnicos que estão de plantão, vinculados a este cliente e que ainda não tem uma SAT de emergência atribuida
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

            // Dispara o job que envia a notificação os técnicos a cada 30 segundos, até que um visualize a SAT, no routes/console.php
            // \App\Jobs\EmergencySatNotifications::dispatch($tec->id, $order->id);
        }
    }

    public function emergencySatNotification($notifiable)
    {
        $notifiable->title = "SAT EMERGENCIAL Nº{$this->id} -  " . $this->client->name . " - ABERTA!";
        $notifiable->order_id = $this->id;
        $notifiable->type = 'emergency_info';
        $notifiable->message = $this->req_descr ?? 'Serviço de Emergência.';
        $notifiable->notify(new NewSampleNotification());
    }

    public function satNotification($notifiable)
    {
        $notifiable->title = "SAT {$this->id} - " . $this->client->name . " - " . $this->client->name . " - aberta!";
        $notifiable->order_id = $this->id;
        $notifiable->type = 'info';
        $notifiable->message = $this->req_descr ?? 'Atividade de manutenção!';
        $notifiable->notify(new NewSampleNotification());
    }

    public function tecSatNotification($notifiable)
    {
        $notifiable->title = 'SAT - ' . $this->id . ' - ' . $this->client->name . ' - atribuída pelo Supervisor!';
        $notifiable->order_id = $this->id;
        $notifiable->message = $this->req_descr ?? 'Atividade de manutenção!';
        $notifiable->notify(new NewSampleNotification());
    }

    public function notifySupsThatTecGetEmergencySat($tec_id)
    {
        $tec = Tec::findOrFail($tec_id);

        $supervisors = User::whereHas('sup')->get();

        foreach ($supervisors as $sup) {
            $sup->title = "SAT {$this->id} - " . $this->client->name . " - visualizada pelo Técnico {$tec->user->name}!";
            $sup->order_id = $this->id;
            $sup->type = 'info';
            $sup->message = $this->req_descr ?? 'Atividade de manutenção!';
            $sup->notify(new NewSampleNotification());
        }
    }

    public function emergencySatTecNotification($tec_id)
    {
        $tec = Tec::find($tec_id);

        if (!$tec) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} não encontrado.");
            return;
        }

        // Verifica se a SAT foi criada ou atualizada
        if (!$this->updated_at || !$this->created_at) {
            $this->update([
                'updated_at' => now(),
                'created_at' => now()
            ]);
        }

        // LIMITE DE 60 MINUTOS: Verifica se a SAT foi criada/atualizada há mais de uma hora e para de enviar notificações
        if ($this->updated_at->diffInMinutes(now()) > 60) {
            Log::info("Send Emergency Alert Stoped: Ciclo de notificações encerrado por tempo limite (60min) para a SAT #{$this->id}, SAT foi criada / atualizada a {$this->updated_at->diffInMinutes(now())}min.");

            // Opcional: Aqui você pode desativar a flag no banco para o card parar de ser emergência
            // ou apenas parar as notificações. Vamos apenas parar as notificações:
            $tec->update(['emergency_notification_pending' => false]);
            return;
        }

        if (!$tec->emergency_notification_pending) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} sem notificações emergenciais ativas no momento (provavelmente já abriu a SAT).");
            return;
        }

        if ($tec->emergency_this_id !== $this->id) {
            Log::info("Send Emergency Alert Stoped: SAT #{$this->id} não está ativa como emergencial para o técnico #{$tec_id}, SAT atual: #{$tec->emergency_this_id}.");
            return;
        }

        if (!$tec->on_call) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} não está de plantão no momento.");
            return;
        }
        try {
            if ($notifiable = User::find($tec->user_id)) {
                // Preparamos os dados para a notificação
                $notifiable->title = '🚨 SAT EMERGENCIAL ' . $this->id . ' - ' . $this->client->name . ' - ABERTA!';
                $notifiable->order_id = $this->id;
                $notifiable->type = 'emergency';
                $notifiable->channel_id = 'emergency';
                $notifiable->message = $this->req_descr ?? 'Manutenção Urgente Pendente!';
                $notifiable->notify(new NewSampleNotification());
            }
        } catch (\Exception $e) {
            Log::error("Falha ao enviar push na emergência #{$this->id}: " . $e->getMessage());
            // Não damos 'return' aqui para que o finally agende a próxima tentativa
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

            // 2. Finaliza a SAT
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
