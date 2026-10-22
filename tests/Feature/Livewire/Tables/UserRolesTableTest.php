<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UserRolesTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserRolesTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UserRolesTable::class)
            ->assertStatus(200);
    }
}
