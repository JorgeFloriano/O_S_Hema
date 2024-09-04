<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
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
}
