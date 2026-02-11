<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}
