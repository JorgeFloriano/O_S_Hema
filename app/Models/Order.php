<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_type_id',
        'client_id',
        'sector',
        'tec_id',
        'user_id',
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
        'cl_sign_path'
    ];

    protected $table = "orders";
    protected $primaryKey = "id";

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function tec(): BelongsTo
    {
        return $this->belongsTo(Tec::class)->withTrashed();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type_id')->withTrashed();
    }

    // Null values will be replaced by - - : - - and the time will be formatted without seconds
    public function notes_time_format()
    {
        foreach ($this->notes as $note) {
            $note->go_start ? $note->go_start = date('H:i', strtotime($note->go_start)) : $note->go_start = ' - - : - -';
            $note->go_end ? $note->go_end = date('H:i', strtotime($note->go_end)) : $note->go_end = ' - - : - -';
            $note->start ? $note->start = date('H:i', strtotime($note->start)) : $note->start = ' - - : - -';
            $note->end ? $note->end = date('H:i', strtotime($note->end)) : $note->end = ' - - : - -';
            $note->back_start ? $note->back_start = date('H:i', strtotime($note->back_start)) : $note->back_start = ' - - : - -';
            $note->back_end ? $note->back_end = date('H:i', strtotime($note->back_end)) : $note->back_end = ' - - : - -';
        }
    }

    public function finish(): bool
    {
        $tecs = Tec::where('emergency_order_id', $this->id)->get();

        $tecs_updated = false;
        if (count($tecs) > 0) {
            foreach ($tecs as $tec) {
                $tec->emergency_order_id = null;
                $tecs_updated = $tec->save();
            }
        }

        $this->finished = true;
        $order_finished = $this->save();

        if ($tecs_updated && $order_finished) {
            return true;
        } else {
            return false;
        }
    }
}
