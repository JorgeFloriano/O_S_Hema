<?php

// app/Console/Commands/ResetEmergencyTecsSat.php
namespace App\Console\Commands;

use App\Models\Tec;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetEmergencyTecsSat extends Command
{
    // O nome do comando que você digitará no terminal
    protected $signature = 'emergency:reset-all-complete';

    // Descrição que aparece no php artisan list
    protected $description = 'Resseta IMEDIATAMENTE os registros de SAT emergencial e ciclos de notificações para cada técnico';

    public function handle()
    {
        $this->warn('Iniciando o reset geral de registros de emergência (incluindo deletados)...');

        // 1. Contamos usando withTrashed() para incluir os técnicos "deletados"
        $query = Tec::withTrashed()
            ->where(function ($q) {
                $q->whereNotNull('emergency_notification_pending')
                    ->orWhereNotNull('emergency_order_id');
            });

        $count = $query->count();

        if ($count === 0) {
            $this->info('Nenhum registro de emergência encontrado (mesmo entre deletados).');
            return 0;
        }

        // 2. Resetamos todos de uma vez
        // O update direto no Builder (com withTrashed) garante que os deletados também sejam limpos
        $query->update([
            'emergency_notification_pending' => false,
            'emergency_order_id' => null
        ]);

        $message = "Sucesso: {$count} técnicos (ativos e deletados) foram retirados do modo de emergência.";

        Log::emergency("RESET GERAL DE ALERTAS COMPLETO: Executado. {$count} técnicos afetados (incluindo registros com SoftDelete).");

        $this->info($message);

        return 0;
    }
}
