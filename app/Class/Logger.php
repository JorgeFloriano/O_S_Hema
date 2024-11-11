<?php

namespace App\Class;
use Illuminate\Support\Facades\Log;

class Logger {
    public function log($level, $message) {

        // Try add auth user to the message...
        if (auth()->user()) {
            $message .= ' - '.auth()->user()->username;
        } else {
            $message .= ' - ANONYMOUS';
        }

        // Register a entry in the log...
        Log::channel('main')->$level($message);
    }
}
