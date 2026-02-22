<?php

namespace App\Class;

use Illuminate\Support\Facades\Log;

class Logger {
    public function log($level, $message) {
        // Pega o rastro da execução
        // DEBUG_BACKTRACE_IGNORE_ARGS economiza memória
        // O limite '2' é para pegar a função atual e a que chamou esta
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        
        // O índice [0] contém onde a função log() foi disparada
        $file = $backtrace[0]['file'] ?? 'unknown';
        $line = $backtrace[0]['line'] ?? 'unknown';

        // Tenta simplificar o caminho do arquivo (opcional)
        $file = str_replace(base_path(), '', $file);

        // Adiciona o usuário
        if (auth()->check()) {
            $message .= ' - User: ' . auth()->user()->username;
        } else {
            $message .= ' - User: ANONYMOUS';
        }

        // Adiciona a localização (Arquivo e Linha)
        $message .= " [at $file:$line]";

        // Registra no canal
        Log::channel('main')->$level($message);
    }
}