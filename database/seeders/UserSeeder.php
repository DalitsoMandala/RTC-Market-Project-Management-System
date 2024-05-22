<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Role::create(['name' => 'internal']);
        Role::create(['name' => 'external']);
        Role::create(['name' => 'organiser']);
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cip']);
        Role::create(['name' => 'desira']);


        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999'
        ])->assignRole(['admin']);

        User::create([
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999'
        ])->assignRole(['internal', 'cip', 'organiser']);

        User::create([
            'name' => 'user_staff',
            'email' => 'user_staff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999'
        ])->assignRole(['internal', 'cip', 'staff']);


        User::create([
            'name' => 'user_partner',
            'email' => 'user_partner@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999'
        ])->assignRole(['external', 'cip', 'staff']);

        //roletype,project,position
    }
}