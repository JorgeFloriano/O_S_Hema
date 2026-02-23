<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
        'function',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adm(): HasOne
    {
        return $this->hasOne(Adm::class);
    }

    public function tec(): HasOne
    {
        return $this->hasOne(Tec::class);
    }

    public function sup(): HasOne
    {
        return $this->hasOne(Sup::class);
    }

    public function cli(): HasOne
    {
        return $this->hasOne(Cli::class);
    }

    public function isAdm(): bool
    {
        return $this->adm ? true : false;
    }

    public function isMainAdm(): bool
    {
        return $this->adm ? $this->adm->isMain() : false;
    }

    public function isTec(): bool
    {
        return $this->tec ? true : false;
    }

    public function isSup(): bool
    {
        if ($this->isMainAdm()) return true;

        return $this->sup ? true : false;
    }

    public function isHemaTeam(): bool
    {
        return $this->isAdm() || $this->isSup() || $this->isTec();
    }

    public function isCli(): bool
    {
        return $this->cli ? true : false;
    }

    public function isCliAdmin(): bool
    {
        return $this->cli ? $this->cli->isAdmin() : false;
    }

    public function clientCanCreateSat(): bool | null
    {
        return $this->cli ? $this->cli->canCreateSat() : false;
    }

    public function clientCanSeeSat(): bool | null
    {
        return $this->cli ? $this->cli->canSeeSat() : false;
    }

    public function clientCanAccessClient($user_id): bool | null
    {
        if (!auth()->id()) return false;

        $user = User::find($user_id);

        if (!$user->userClientId()) return false;

        return $this->isCli() ? $this->cli->canAccessClient($user->userClientId()) : false;
    }

    public function userClientId(): int | bool
    {
        return $this->cli ? $this->cli->id : false;
    }

    public function canAcessClientsAndMaterials(): bool
    {
        if (!$this->adm) {
            return false;
        }

        if ($this->adm->main) {
            return true;
        }

        return $this->adm->cli ? true : false;
    }

    public function canSeeSat(): bool
    {
        return $this->hasPermission('sats', 1) || $this->clientCanSeeSat();
    }

    public function canCreateSat(): bool
    {
        // If user is a client with access to create sat or is a supervisor, return true
        return $this->clientCanCreateSat() || $this->hasPermission('sats', 2);
    }

    public function canDeleteSat($order): bool
    {
        if ($this->isMainAdm()) return true;

        if (($this->isSup() || $this->isAdm()) && !$order->finished) return true;

        return false;
    }

    public function clientStringForUnlabeledDList(): string
    {
        return $this->cli ? $this->cli->stringForUnlabeledDList() : 'Cliente não encontrado para usuário';
    }

    public function userClientCompanyId(): int | null
    {
        if (!$this->cli) {
            return null;
        }
        return $this->cli->clientId();
    }

    public function userClientCompanyName(): string | null
    {
        if (!$this->cli) {
            return null;
        }
        return $this->cli->clientName();
    }

    /**
     * RELACIONAMENTO com tokens Expo
     */
    public function expoTokens(): HasMany
    {
        return $this->hasMany(ExpoToken::class);
    }

    /**
     * MÉTODO CORRIGIDO para notificações Expo
     * Retorna apenas o token MAIS RECENTE
     */
    public function getLatestToken()
    {
        // Busca o token mais recente deste usuário
        $latestToken = $this->expoTokens()
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc') // Desempate
            ->first();

        // Se não encontrar token, retorna array vazio
        if (!$latestToken) {
            return [];
        }

        // Retorna APENAS o token mais recente em array
        return [$latestToken->value];

        // Ou se quiser enviar para TODOS os tokens válidos do usuário:
        // return $this->expoTokens()->pluck('value')->toArray();
    }

    public function getLatestsTokens()
    {
        // Pega os 3 tokens mais recentes
        return $this->expoTokens()
            ->latest('created_at')
            ->limit(3)
            ->pluck('value')
            ->toArray();
    }

    /**
     * Método para obter o token atual (mais recente)
     * Útil para debug ou outras operações
     */
    public function getCurrentExpoTokenAttribute()
    {
        return $this->expoTokens()->latest('created_at')->first();
    }

    // Checks if the user is allowed to edit another user
    public function editUserPermission($user_id)
    {

        if (!$this->adm) {
            return false;
        }

        if (!$this->adm->main) {
            return false;
        }

        $user_adm = Adm::where('user_id', $user_id)->first();

        if ($user_adm) {
            if ($user_adm->main) {
                if ($user_id == $this->id) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return true;
    }

    // Delete user and all of his acess
    public function CompletelyDelete()
    {
        $this->adm()->delete();
        $this->cli()->delete();
        $this->tec()->delete();
        $this->sup()->delete();
        return parent::delete();
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function reopenOrder($order_id)
    {
        Gate::authorize('check-permission', ['reopen_sat', 1]);

        $order = Order::find($order_id);
        $order_reopened = $order->finished = 0;
        $order_reopened = $order->save();
        return $order_reopened;
    }

    public function resetAllEmergencies()
    {
        // Chama o comando que criamos internamente
        Artisan::call('emergency:reset-all');
    }

    public function cantCreateSatMessage(): string
    {
        return 'Usuário sem permissão para criar SATs.';
    }

    // Scopes
    // Get default clients only
    public function scopeOnlyDefaultClients($query, $client_id)
    {
        return $query->whereHas('cli', function ($q) use ($client_id) {
            $q->where('client_id', $client_id)->whereNull('is_admin');
        })
            ->notHemaTeam();
    }

    public function scopeNotHemaTeam($query)
    {
        return $query
            ->whereDoesntHave('adm')
            ->whereDoesntHave('sup')
            ->whereDoesntHave('tec');
    }

    public function scopeOnlyHemaTeam($query)
    {
        return $query
            ->whereHas('adm')
            ->orWhereHas('sup')
            ->orWhereHas('tec');
    }

    /**
     * Relacionamento com as Permissões
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('access_level')
            ->withTimestamps();
    }

    /**
     * Método para verificar permissão e nível de acesso
     * @param string $permissionName O nome da permissão (ex: 'sats')
     * @param int $level 1 para leitura, 2 para escrita/completo
     */
    public function hasPermission(string $permissionName, int $level = 1): bool
    {
        if ($this->isMainAdm()) {
            return true;
        }

        // Busca a permissão dentro da coleção carregada do usuário
        $permission = $this->permissions->where('name', $permissionName)->first();

        //return true;

        if (!$permission) {
            return false;
        }

        // Retorna true se o nível do usuário for maior ou igual ao exigido
        return $permission->pivot->access_level >= $level;
    }

    public function permissionStyle(string $permission): array
    {
        if (!$this->hasPermission($permission)) {
            return [
                'bg' => 'white',
                'text' => 'black',
            ];
        }

        if ($this->hasPermission($permission, 2)) {
            return [
                'bg' => '#d1e7dd',
                'text' => '#0f5132',
            ];
        }

        return [
                'bg' => '#e2e3e5',
                'text' => '#6c757d',
            ];
    }

    public function profileRoute(): string
    {

        if ($this->isCliAdmin()) {
            return route('client.users.edit', ['user' => Crypt::encryptString($this->id)]);
        }
        if ($this->isMainAdm()) {
            return route('users.edit', ['user' => Crypt::encryptString($this->id)]);
        }

        return route('users.show', ['user' => Crypt::encryptString($this->id)]);
    }
}
