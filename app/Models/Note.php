<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'equip_mod',
        'equip_id',
        'equip_type',
        'situation',
        'cause',
        'services',
        'date',
        'go_start',
        'go_end',
        'start',
        'end',
        'back_start',
        'back_end',
        'food',
        'km_start',
        'km_end',
        'expense',
        'obs',
    ];

    protected $table = "notes";
    protected $primaryKey = "id";

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tecs(): BelongsToMany
    {
        return $this->belongsToMany(Tec::class)->withPivot('signature')->withTimestamps()->withTrashed();
    }

}
