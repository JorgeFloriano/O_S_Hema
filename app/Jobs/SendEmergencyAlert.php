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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendEmergencyAlert implements ShouldQueue
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
    }

    private function isEmergencyHour(): bool
    {
        // Pega o dia atual (0 a 6) e o horário atual
        $now = now(); // Carbon instance
        $dayOfWeek = $now->dayOfWeek; // 0 (Dom) - 6 (Sáb)
        $currentTime = $now->format('H:i:s');

        // Busca a configuração para o dia de hoje
        $schedule = DB::table('business_hours')
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Se não houver configuração para o dia (ex: domingo sem registro), 
        // podemos considerar emergência total
        if (!$schedule) return true;

        // Se o horário atual for ANTES do início ou DEPOIS do fim, é emergência
        if ($currentTime < $schedule->start_time || $currentTime > $schedule->end_time) {
            return true;
        }

        return false; // Está dentro do horário comercial
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // Verificação de Horário (Só prossegue se FOR horário de emergência)
        if (!$this->isEmergencyHour()) {
            // Opcional: Log para depuração
            Log::info("Job abortado: Horário comercial detectado.");
            return;
        }

        $tec = Tec::find($this->tecId);
        $order = Order::find($this->orderId);

        // LIMITE DE 60 MINUTOS: Verifica se a ordem foi atualizada há mais de uma hora
        if ($order->updated_at->diffInMinutes(now()) > 60) {
            Log::info("Ciclo de notificações encerrado por tempo limite (60min) para a SAT #{$this->orderId}, ordem foi atualizada a {$order->updated_at->diffInMinutes(now())}min.");

            // Opcional: Aqui você pode desativar a flag no banco para o card parar de ser emergência
            // ou apenas parar as notificações. Vamos apenas parar as notificações:
            $tec->update(['emergency_notification_pending' => false]);
            return;
        }

        // Verifica se o técnico ainda tem notificações pendentes para ESTA ordem, se está de plantão, se ainda não verificou a ordem de emrgência
        if (
            $tec &&
            $tec->emergency_notification_pending &&
            $tec->emergency_order_id == $this->orderId &&
            ($tec->on_call == 1 || $tec->on_call == true)
        ) {

            if ($notifiable = User::find($tec->user_id)) {
                // Preparamos os dados para a notificação
                $notifiable->title = 'SOLICITAÇÃO DE EMERGÊNCIA!';
                $notifiable->order_id = $this->orderId;
                $notifiable->emergency = true;
                $notifiable->message = $order->req_descr ?? 'Manutenção Urgente Pendente!';

                $notifiable->notify(new NewSampleNotification());
            }

            // Agenda o próximo reenvio
            // IMPORTANTE: passamos os IDs novamente para o construtor do novo Job
            self::dispatch($this->tecId, $this->orderId)->delay(now()->addSeconds(30));
        }
    }
}
