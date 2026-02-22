<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('logger_main')) {
    /**
     * Helper global para log customizado com arquivo, linha e usuário.
     */
    function logger_main($level, $message) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file = isset($backtrace[0]['file']) ? str_replace(base_path(), '', $backtrace[0]['file']) : 'unknown';
        $line = $backtrace[0]['line'] ?? 'unknown';

        $user = auth()->check() ? auth()->user()->username : 'ANONYMOUS';
        
        $finalMessage = "{$message} - User: {$user} [at {$file}:{$line}]";

        Log::channel('main')->$level($finalMessage);
    }
}