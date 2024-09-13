<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'writer_id',
        'user_id',
        'type',
        'finished',
        'equipment',
        'req_date',
        'req_time',
        'req_descr',
    ];


    protected $table = "orders";
    protected $primaryKey = "id";

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }
}
