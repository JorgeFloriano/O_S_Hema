<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Artisan;
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
        return $this->sup ? true : false;
    }

    public function isCli(): bool
    {
        return $this->cli ? true : false;
    }

    public function isCliAdmin(): bool
    {
        return $this->cli ? $this->cli->isAdmin() : false;
    }

    public function clientCanCreateSat(): bool
    {
        return $this->cli ? $this->cli->canCreateSat() : false;
    }

    public function clientCanSeeSat(): bool
    {
        return $this->cli ? $this->cli->canSeeSat() : false;
    }

    public function clientId(): int | null
    {
        return $this->cli ? $this->cli->clientId() : null;
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

    public function resetAllEmergencies()
    {
        // Chama o comando que criamos internamente
        Artisan::call('emergency:reset-all');
    }

    public function canCreateSat(): bool
    {
        // Verify if client user has access to create sat
        $cli_can = false;
        if ($this->cli) {
            $cli_can = $this->cli->can_create_sat;
        }

        // Verify if user has supervisor access
        $sup_can = false;
        if ($this->sup) {
            $sup_can = $this->sup;
        }

        // If user is a client with access to create sat or is a supervisor, return true
        return $cli_can || $sup_can;
    }
    public function cantCreateSatMessage(): string
    {
        return 'Usuário sem permissão para criar SATs.';
    }
}
