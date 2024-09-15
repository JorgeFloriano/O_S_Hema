<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'adm_id',
        'tec_id',
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

    public function adm(): BelongsTo {
        return $this->belongsTo(Adm::class);
    }

    public function tec(): BelongsTo {
        return $this->belongsTo(Tec::class);
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }
}
