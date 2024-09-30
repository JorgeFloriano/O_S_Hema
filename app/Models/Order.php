<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'sector',
        'tec_id',
        'user_id',
        'type',
        'finished',
        'equipment',
        'req_name',
        'req_date',
        'req_time',
        'req_descr',
        'cl_name',
        'cl_function',
        'cl_contact',
        'cl_date',
        'cl_sign',
    ];

    protected $table = "orders";
    protected $primaryKey = "id";

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function tec(): BelongsTo {
        return $this->belongsTo(Tec::class)->withTrashed();
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }
}
