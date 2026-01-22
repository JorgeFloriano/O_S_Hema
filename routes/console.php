<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Agendando o seu comando customizado
Schedule::command('emergency:reset-all-complete')
    ->weekdays()       // Segunda a Sexta
    ->at('09:00')      // Horário exato
    ->timezone('America/Sao_Paulo'); // Garante o horário de Brasília
    
// Agendamento maestro - agora sem atrasos
// Schedule::call(function () {
//     // Cria uma trava rápida de 50 segundos. 
//     // Isso garante que NADA duplique se o Cron for chamado duas vezes por erro do servidor.
//     $lock = Cache::lock('maestro-emergencia-lock', 50);

//     if ($lock->get()) {
//         $tecnicos = \App\Models\Tec::where('emergency_notification_pending', true)
//             ->where('on_call', true)
//             ->whereNotNull('emergency_order_id')
//             ->get();

//         foreach ($tecnicos as $tec) {
//             // Dispara o Job
//             \App\Jobs\EmergencySatNotifications::dispatch($tec->id, $tec->emergency_order_id);
//             Log::info("Maestro: Disparado para Técnico #{$tec->id}");
//         }
        
//         // Opcional: Liberar o lock após processar (ou deixar expirar sozinho em 50s)
//         // $lock->release(); 
//     } else {
//         Log::info("Maestro: Bloqueado (Processo já em execução ou travado)");
//     }
// })->everyMinute();