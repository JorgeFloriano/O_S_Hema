<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\SoftDeletes;


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
}
