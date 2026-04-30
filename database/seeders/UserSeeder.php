<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@cfarm.local']);

        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }

        $admin->name = $admin->name ?: 'Administrator';
        $admin->role = 'admin';
        $admin->save();

        User::firstOrCreate(
            ['email' => 'operator@cfarm.local'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]
        );
    }
}
