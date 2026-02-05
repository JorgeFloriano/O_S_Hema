<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cli extends Model
{
    // For Clients users

    use HasFactory;
    use SoftDeletes;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'client_id',
        'user_id',
        'is_admin',
        'can_create_sat',
        'can_see_sat',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function canCreateSat(): bool
    {
        return $this->isAdmin() ? true : $this->can_create_sat;
    }

    public function canSeeSat(): bool
    {
        return $this->isAdmin() ? true : $this->can_see_sat;
    }

    public function clientId(): int
    {
        $client_id = Client::find($this->client_id)->id;

        if (!$client_id) {
            return 'ID do cliente nao encontrado';
        }

        return $client_id;
    }

    public function clientName(): string
    {
        $client_name = Client::find($this->client_id)->name;

        if (!$client_name) {
            return 'Cliente não encontrado';
        }

        return $client_name;
    }

    public function stringForUnlabeledDList(): string
    {
        $client = Client::find($this->client_id);

        if (!$client) {
            return 'Cliente não encontrado';
        }

        return $client->name . ' - [' . $client->id . ']';
    }
}
