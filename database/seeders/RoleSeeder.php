<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear roles (firstOrCreate evita errores si ya existen)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $roleCliente = Role::firstOrCreate(['name' => 'Cliente']);

        // 2. Crear SuperAdmin
        $admin = User::firstOrCreate(
            ['email' => 'admin@losandes.com'], // Busca por este correo
            [
                'name' => 'Alex Joel',
                'ap_pat' => 'Quispe',
                'ap_mat' => 'Ticona',
                'password' => Hash::make('12345678'),
            ]
        );
        $admin->assignRole($roleSuperAdmin);

        // 3. Crear Vendedor
        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor1@losandes.com'],
            [
                'name' => 'Cris',
                'ap_pat' => 'Lazcano',
                'ap_mat' => 'Gutierrez',
                'password' => Hash::make('vendedor123'),
            ]
        );
        $vendedor->assignRole($roleVendedor);
$vendedor = User::firstOrCreate(
            ['email' => 'said@losandes.com'],
            [
                'name' => 'Sais',
                'ap_pat' => 'Mamani',
                'ap_mat' => 'Condori',
                'password' => Hash::make('said123'),
            ]
        );
        $vendedor->assignRole($roleVendedor);
         $vendedor->assignRole($roleVendedor);
$vendedor = User::firstOrCreate(
            ['email' => 'said@losandes.com'],
            [
                'name' => 'Sais',
                'ap_pat' => 'Mamani',
                'ap_mat' => 'Condori',
                'password' => Hash::make('said123'),
            ]
        );
        $vendedor->assignRole($roleVendedor);

        // 4. Crear Cliente
        $cliente = User::firstOrCreate(
            ['email' => 'cliente@losandes.com'],
            [
                'name' => 'Ana',
                'ap_pat' => 'Luz',
                'ap_mat' => 'Rios',
                'password' => Hash::make('cliente123'),
            ]
        );
        $cliente->assignRole($roleCliente);
        
    }
}