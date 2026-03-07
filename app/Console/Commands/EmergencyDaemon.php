<?php

// app/Console/Commands/EmergencyDaemon.php
namespace App\Console\Commands;

use App\Class\Hours;
use Illuminate\Console\Command;
use App\Models\Tec;
use App\Jobs\EmergencySatNotifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmergencyDaemon extends Command
{
    // O nome que você usará no Cron
    protected $signature = 'emergency:daemon';

    public function handle()
    {
        $lock = Cache::lock('emergency-daemon-running', 840); // Trava por 14 min

        if (!$lock->get()) {
            logger_main('error', 'Já existe um Daemon rodando. Encerrando para evitar duplicidade.');
            $this->info('Já existe um Daemon rodando. Encerrando para evitar duplicidade.');
            return;
        }

        // 840 segundos = 14 minutos. 
        // Encerramos 1 min antes do próximo Cron para não encavalar processos.
        $limiteDeTempo = 840;
        $inicio = time();
        // Verificação de Horário de Emergência
        $hours = new Hours();

        Log::debug("Daemon de Emergência iniciado.");

        while (((time() - $inicio) < $limiteDeTempo) && $hours->isEmergency()) {

            // Lógica do Maestro Otimizada
            $tecnicos = Tec::where('emergency_notification_pending', true)
                ->where('on_call', true)
                ->whereNotNull('emergency_order_id')
                ->whereIn('emergency_order_id', function ($query) {
                    $query->select('id')
                        ->from('orders')
                        ->where('finished', false); // Garante que só pega as abertas
                })
                ->get();

            foreach ($tecnicos as $tec) {
                // Atualiza o updated_at do técnico AGORA
                $tec->touch(); 
                
                EmergencySatNotifications::dispatch($tec->id, $tec->emergency_order_id);
                Log::info("Loop Interno: Disparado para Técnico #{$tec->id}");
            }

            // Importante para evitar que a conexão com o banco caia por inatividade
            \Illuminate\Support\Facades\DB::disconnect();

            // O SEGREDO: Sleep consome ZERO de CPU e não abre novas conexões MySQL
            // Dormimos 60 segundos
            sleep(60);
        }

        $lock->release();
        Log::debug("Daemon de Emergência encerrando para renovação do Cron.");
    }
}
