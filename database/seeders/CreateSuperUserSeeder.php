<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Superuser', 
            'email' => 'superuser@gmail.com', 
            'password' => 'Password.1'
        ]);
        $role = Role::create(['name' => 'superuser']); 
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions); 
        $user->assignRole([$role->id]);
    }
}
