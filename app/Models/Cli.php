<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cli extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'client_id',
        'user_id',
        'is_admin',
        'can_create_sat',
        'can_see_sat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function isAdmin(): bool
    {
        return $this->is_admin ? true : false;
    }

    public function canCreateSat(): bool | null
    {
        return $this->isAdmin() ? true : $this->can_create_sat;
    }

    public function canSeeSat(): bool | null
    {
        return $this->isAdmin() ? true : $this->can_see_sat;
    }

    public function canAccessClient($client_id): bool
    {
        // Verify if logged user is a client admin
        if (!$this->isAdmin()) return false;

        // Verify if client exists
        $client = Cli::find($client_id);
        if (!$client) return false;

        // Verify if logged user and user that is being accessed are the same company
        if ($client->clientId() != $this->clientId()) return false;

        // Verify if user that is being accessed is a client admin and is not the logged user
        if ($client->isAdmin() && ($client->user_id != $this->user_id)) return false;
        
        return true;
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
