<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Setup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SetupTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Setup::class)
            ->assertStatus(200);
    }
}
