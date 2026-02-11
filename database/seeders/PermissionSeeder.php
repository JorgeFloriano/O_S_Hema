<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            ['name' => 'sats', 'description' => 'Gerenciar Assistências Técnicas'],
            ['name' => 'users', 'description' => 'Gerenciar Usuários'],
            ['name' => 'materials', 'description' => 'Cadastro de Materiais'],
            ['name' => 'clients', 'description' => 'Cadastro de Clientes'],
            ['name' => 'codes', 'description' => 'Cadastro de Códigos'],
            ['name' => 'reopen_sat', 'description' => 'Reabrir SAT'],
            ['name' => 'attach_tec', 'description' => 'Vincular Técnico'],
            ['name' => 'manager_on_call', 'description' => 'Gerenciar Sobreaviso'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}
