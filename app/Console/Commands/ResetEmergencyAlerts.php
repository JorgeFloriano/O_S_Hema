<?php

namespace App\Console\Commands;

use App\Models\Tec;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetEmergencyAlerts extends Command
{
    // O nome do comando que você digitará no terminal
    protected $signature = 'emergency:reset-all';

    // Descrição que aparece no php artisan list
    protected $description = 'Interrompe IMEDIATAMENTE todos os loops de notificações de emergência de todos os técnicos';

    public function handle()
    {
        $this->warn('Iniciando o reset geral de notificações de emergência...');

        // 1. Contamos quantos técnicos estão em estado de alerta para o log
        $count = Tec::where('emergency_notification_pending', true)->count();

        if ($count === 0) {
            $this->info('Nenhum alerta ativo encontrado.');
            return 0;
        }

        // 2. Resetamos todos os técnicos de uma vez
        // Isso fará com que todos os Jobs EmergencySatNotifications morram no próximo ciclo (handle)
        Tec::query()->update([
            'emergency_notification_pending' => null,
            'emergency_order_id' => null
        ]);

        $message = "Sucesso: {$count} técnicos foram retirados do modo de emergência.";

        Log::emergency("RESET GERAL DE ALERTAS: Executado manualmente via console. {$count} técnicos afetados.");

        $this->info($message);

        return 0;
    }
}
