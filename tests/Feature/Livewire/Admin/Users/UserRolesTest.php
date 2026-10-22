<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UserRoles::class)
            ->assertStatus(200);
    }
}
