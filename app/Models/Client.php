<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj_cpf',
        'cep',
        'unit',
        'address',
        'email',
        'phone',
        'contact',
    ];

    protected $table = "clients";
    protected $primaryKey = "id";
}
