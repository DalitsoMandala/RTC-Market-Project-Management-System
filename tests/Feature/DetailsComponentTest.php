<?php

namespace Tests\Feature;

use App\Models\Organisation;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Profile\Details;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DetailsComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_loads_user_data()
    {

        Role::create(['name' => 'admin']);

        $organisation = Organisation::create([
            'name' => 'Test Organisation',

        ]);

        $user = User::create([
            'name' => 'Eliya Kapalasa2',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+9999999999',
            'organisation_id' => $organisation->id,
            'image' => 'eliya.jpg'
        ])->assignRole([
                    'admin',

                ]);

        $this->actingAs($user);

        Livewire::test(Details::class)
            ->assertSet('email', $user->email)
            ->assertSet('username', $user->name);
    }
}
