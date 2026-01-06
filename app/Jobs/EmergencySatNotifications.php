<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Tec;
use App\Models\User;
use App\Notifications\NewSampleNotification;
use Illuminate\Bus\Queueable; // Verifique se está usando Bus\Queueable
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmergencySatNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tecId;

    /**
     * Agora o construtor recebe e armazena os IDs
     */
    public function __construct(int $tecId, int $orderId)
    {
        $this->tecId = $tecId;
        $this->orderId = $orderId;
        $this->queue = 'emergency';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tec = Tec::find($this->tecId);
        $order = Order::find($this->orderId);

        if (!$tec) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} não encontrado.");
            return;
        }

        if (!$order) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stopped: SAT #{$this->orderId} não encontrada.");
            return;
        }

        // LIMITE DE 60 MINUTOS: Verifica se a ordem foi criada/atualizada há mais de uma hora e para de enviar notificações
        if ($order->updated_at->diffInMinutes(now()) > 60) {
            Log::info("Send Emergency Alert Stoped: Ciclo de notificações encerrado por tempo limite (60min) para a SAT #{$this->orderId}, ordem foi criada / atualizada a {$order->updated_at->diffInMinutes(now())}min.");

            // Opcional: Aqui você pode desativar a flag no banco para o card parar de ser emergência
            // ou apenas parar as notificações. Vamos apenas parar as notificações:
            $tec->update(['emergency_notification_pending' => false]);
            return;
        }

        if (!$tec->emergency_notification_pending) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} sem notificações emergenciais ativas no momento (provavelmente já abriu a SAT).");
            return;
        }

        if ($tec->emergency_order_id !== $this->orderId) {
            Log::info("Send Emergency Alert Stoped: SAT #{$this->orderId} não está ativa como emergencial para o técnico #{$this->tecId}, ordem atual: #{$tec->emergency_order_id}.");
            return;
        }

        if (!$tec->on_call) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} não está de plantão no momento.");
            return;
        }

        if ($notifiable = User::find($tec->user_id)) {
            // Preparamos os dados para a notificação
            $notifiable->title = 'SAT EMERGENCIAL - ' . $order->id . ' - ' . $order->client->name . ' - ABERTA!';
            $notifiable->order_id = $this->orderId;
            $notifiable->type = 'emergency';
            $notifiable->channel_id = 'emergency';
            $notifiable->message = $order->req_descr ?? 'Manutenção Urgente Pendente!';
            $notifiable->notify(new NewSampleNotification());
        }

        // Agenda o próximo reenvio
        // IMPORTANTE: passamos os IDs novamente para o construtor do novo Job
        self::dispatch($this->tecId, $this->orderId)->delay(now()->addSeconds(30));
    }
}
