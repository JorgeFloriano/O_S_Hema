<?php

namespace App\Class;

use Illuminate\Support\Facades\DB;
class Hours {

    // Verifica se estamos em horário comercial
    public function isEmergency(): bool
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
}
