<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        function getOrganisationId($name)
        {
            $org = Organisation::where('name', $name)->first();

            return $org->id;
        }

        Role::create(['name' => 'internal']);
        Role::create(['name' => 'external']);
        //
        Role::create(['name' => 'organiser']);
        Role::create(['name' => 'staff']);
        //  Role::create(['name' => 'assistant']);
        Role::create(['name' => 'admin']);
        // Role::create(['name' => 'donor']);
        //
        Role::create(['name' => 'cip']);
        //  Role::create(['name' => 'desira']);

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 1,
        ])->assignRole(['admin']);

        // User::create([
        //     'name' => 'Jamila',
        //     'email' => 'donor@example.com',
        //     'password' => Hash::make('password'),
        //     'phone_number' => '+9999999999',
        //     'organisation_id' => 11,
        // ])->assignRole(['external', 'donor']);

        User::create([
            'name' => 'Eliya Kapalasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 1,
            'image' => 'eliya.jpg'
        ])->assignRole([
                    'internal',
                    'cip',
                    'organiser'
                ]);

        User::create([
            'name' => 'Dalitso Mandala',
            'email' => 'cip@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 1,
        ])->assignRole([
                    'internal',
                    'cip',
                    'organiser'
                ]);

        User::create([
            'name' => 'Thoko Mvula',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 1,
        ])->assignRole([
                    'internal',
                    'cip',
                    'staff'
                ]);

        // CIP internal

        User::create([
            'name' => 'John Smith',
            'email' => 'iita@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 3,
        ])->assignRole([
                    'external',
                    'organiser'
                ]);

        User::create([
            'name' => 'John Mateck',
            'email' => 'tradeline@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 7,
        ])->assignRole([
                    'external',
                    'organiser'
                ]);

        User::create([
            'name' => 'Jack Smith',
            'email' => 'dcd@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 5,
        ])->assignRole([
                    'external',
                    'organiser'
                ]);

        User::create([
            'name' => 'Janet Park',
            'email' => 'daes@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 4,
        ])->assignRole([
                    'external',
                    'organiser'
                ]);

        User::create([
            'name' => 'Janet Malumbo',
            'email' => 'mot@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('MINISTRY OF TRADE'),
        ])->assignRole([
                    'external',
                    'organiser'
                ]);


        //roletype,project,position
    }
}
