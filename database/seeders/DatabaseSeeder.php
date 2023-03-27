<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->firstOrCreate([
            'name' => "Sina",
            'username' => 'sina',
        ],
        [ 'password' => bcrypt('123'),]);

        $role = Role::query()->firstOrCreate([
            'name' => 'super_admin',
        ]);
        !$user->hasRole($role->name)?$user->roles()->attach($role->id):null;

        Role::query()->firstOrCreate([
            'name' => 'manager',
        ]);
        Role::query()->firstOrCreate([
            'name' => 'user',
        ]);

    }
}
