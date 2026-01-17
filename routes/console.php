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
    
// Agendamento maestro de notificações de emergência
Schedule::call(function () {
    $tecnicosParaNotificar = \App\Models\Tec::where('emergency_notification_pending', true)
        ->where('on_call', true)
        ->whereNotNull('emergency_order_id')
        ->get();

    foreach ($tecnicosParaNotificar as $tec) {
        // Dispara o Job de envio para a fila
        \App\Jobs\EmergencySatNotifications::dispatch($tec->id, $tec->emergency_order_id);
    }
})->everyMinute();
