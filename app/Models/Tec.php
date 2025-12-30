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
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'on_call',
        'emergency_order_id',
        'emergency_notification_pending',
    ];

    public function emergencyClients()
    {
        return $this->belongsToMany(Client::class, 'client_tec');
    }
}
