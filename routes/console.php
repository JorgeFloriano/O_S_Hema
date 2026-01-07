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
