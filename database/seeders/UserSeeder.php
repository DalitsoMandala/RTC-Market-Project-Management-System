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

        //   Role::create(['name' => 'internal']); //CIP or DESIRA
        Role::create(['name' => 'external']); // ANY OTHER
        Role::create(['name' => 'manager']); // ANY ONE WHO CAN DO MOST MANIPULATION
        Role::create(['name' => 'staff']); // LIMITED FUNCTIONALITY
        Role::create(['name' => 'admin']); // FULL FUNCTIONALITY
        Role::create(['name' => 'project_manager']); // MANAGE OR VIEW REPORTS AND VISUALIZATIONS
        Role::create(['name' => 'cip']); // PROJECT

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => 1,
        ])->assignRole(['admin']);


        User::create([
            'name' => 'Dalitso Mandala',
            'email' => 'cip@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('CIP'),
        ])->assignRole([
            //   'cip',
            'manager'
        ]);

        User::create([
            'name' => 'Alex Chase',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('CIP'),
        ])->assignRole([

            //    'cip',
            'project_manager'
        ]);


        User::create([
            'name' => 'Thoko Mvula',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('CIP'),
        ])->assignRole([

            //    'cip',
            'staff'
        ]);

        // CIP internal

        User::create([
            'name' => 'John Smith',
            'email' => 'iita@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('IITA'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'John Mateck',
            'email' => 'tradeline@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('TRADELINE'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'Jack Smith',
            'email' => 'dcd@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('DCD'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'Janet Park',
            'email' => 'daes@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('DAES'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'Marah Malumbo',
            'email' => 'mot@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('MINISTRY OF TRADE'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'Mary Malumbo',
            'email' => 'ace@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('ACE'),
        ])->assignRole([
            'external',

        ]);

        User::create([
            'name' => 'Janet Jackie Malumbo',
            'email' => 'dars@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('DARS'),
        ])->assignRole([
            'external',

        ]);


        User::create([
            'name' => 'Patrick John Malembe',
            'email' => 'rtcdt@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => getOrganisationId('RTCDT'),
        ])->assignRole([
            'external',

        ]);
        //roletype,project,position
    }
}
