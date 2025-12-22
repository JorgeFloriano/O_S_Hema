<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    public function isTec() {}

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

    /**
     * RELACIONAMENTO com tokens Expo
     */
    public function expoTokens()
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

    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }
}
