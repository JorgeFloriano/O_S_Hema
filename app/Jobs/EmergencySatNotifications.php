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
    // Adicione essa propriedade dentro da classe
    // public $uniqueFor = 20; // O lock de unicidade expira em 20 segundos
    public $timeout = 20; // O Job expira em 20 segundos

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
     * Determina por quanto tempo o Job pode ser tentado.
     * Se o worker ficar offline e o Job expirar, o Laravel o descarta.
     */
    public function retryUntil()
    {
        return now()->addHours(1); // Tenta re-executar por até 1 hora se o worker cair
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        Log::info("--- Executando Job de Emergência: Técnico #{$this->tecId} / SAT #{$this->orderId} ---");
        
        $tec = Tec::find($this->tecId);

        // Pega apenas o necessário da SAT e o nome do Cliente
        $order = Order::with(['client:id,name'])
            ->select('id', 'client_id', 'req_descr', 'updated_at') // req_descr é necessário para a message
            ->find($this->orderId);

        if (!$tec) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} não encontrado.");
            return;
        }

        if (!$order) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stopped: SAT #{$this->orderId} não encontrada.");
            return;
        }

        // Resseta o ciclo de notificações para o Técnico por timeout
        $tec->timeoutResetSatEmergencyCondition($order, 60);

        if (!$tec->emergency_notification_pending) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} sem notificações emergenciais ativas no momento (provavelmente já abriu a SAT).");
            return;
        }

        if ($tec->emergency_order_id !== $this->orderId) {
            Log::info("Send Emergency Alert Stoped: SAT #{$this->orderId} não está ativa como emergencial para o técnico #{$this->tecId}, SAT atual: #{$tec->emergency_order_id}.");
            return;
        }

        if (!$tec->on_call) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stoped: Técnico #{$this->tecId} não está de plantão no momento.");
            return;
        }
        try {
            if ($tec && $notifiable = User::find($tec->user_id)) {
                // Preparamos os dados para a notificação
                $notifiable->title = '🚨 SAT EMERGENCIAL ' . $order->id . ' - ' . $order->client->name . ' - ABERTA!';
                $notifiable->order_id = $this->orderId;
                $notifiable->type = 'emergency';
                $notifiable->channel_id = 'emergency';
                $notifiable->message = $order->req_descr ?? 'Manutenção Urgente Pendente!';
                $notifiable->notify(new NewSampleNotification());
                Log::info("Notificação enviada para Técnico #{$this->tecId}");
            }
        } catch (\Exception $e) {
            Log::error("Erro no envio: " . $e->getMessage());
        }
    }
}

