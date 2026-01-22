<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Tec extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $fillable = [
        'user_id',
        'on_call',
        'emergency_order_id',
        'emergency_notification_pending',
    ];

    public function emergencyClients()
    {
        return $this->belongsToMany(Client::class, 'client_tec', 'tec_id', 'client_id');
    }

    public function emergencyOrder()
    {
        return $this->belongsTo(Order::class, 'emergency_order_id');
    }

    public function resetSatEmergencyCondition()
    {
        return $this->update([
            'emergency_order_id' => null,
            'emergency_notification_pending' => false,
        ]);
    }

    public function timeoutResetSatEmergencyCondition( Order $order, int $timeout = 60): void
    {
        // Verifica se a SAT foi criada ou atualizada
        if (!$order->updated_at || !$order->created_at) {
            $order->update([
                'updated_at' => now(),
                'created_at' => now()
            ]);
        }

        // LIMITE DE 60 MINUTOS: Verifica se a SAT foi criada/atualizada há mais de uma hora e para de enviar notificações
        if ($order->updated_at->diffInMinutes(now()) > $timeout) {
            Log::info("Send Emergency Alert Stoped: Ciclo de notificações encerrado por tempo limite ({$timeout}min) para a SAT #{$this->orderId}, SAT foi criada / atualizada a {$order->updated_at->diffInMinutes(now())}min.");

            // Para as notificações e limpa o "emergency_order_id"
            $this->resetSatEmergencyCondition();
        }
    }
}
