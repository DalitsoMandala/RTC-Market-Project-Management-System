<?php

namespace Database\Seeders;

use App\Models\User;
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
        //
        Role::create(['name' => 'organiser']);
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'admin']);
        //
        Role::create(['name' => 'cip']);
        Role::create(['name' => 'desira']);
        Role::create(['name' => 'iita']);
        Role::create(['name' => 'daes']);
        Role::create(['name' => 'dcd']);
        Role::create(['name' => 'min_of_trade']);
        Role::create(['name' => 'tradeline']);
        Role::create(['name' => 'dars']);
        Role::create(['name' => 'rtcdt']);
        Role::create(['name' => 'ace']);

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['admin']);

        User::create([
            'name' => 'Eliya Kapalasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['internal', 'cip', 'organiser']);

        User::create([
            'name' => 'Pemphero Jere',
            'email' => 'organiserp@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['internal', 'cip', 'organiser']);

        User::create([
            'name' => 'George Mvula',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['internal', 'cip', 'staff']);

        // CIP internal

        User::create([
            'name' => 'John Smith',
            'email' => 'iita@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['external', 'iita', 'organiser']);

        User::create([
            'name' => 'Jack Smith',
            'email' => 'dcd@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['external', 'dcd', 'organiser']);

        User::create([
            'name' => 'Janet Park',
            'email' => 'daes@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
        ])->assignRole(['external', 'daes', 'organiser']);

        //roletype,project,position
    }
}
