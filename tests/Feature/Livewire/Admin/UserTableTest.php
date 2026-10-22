<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UserTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UserTable::class)
            ->assertStatus(200);
    }
}
