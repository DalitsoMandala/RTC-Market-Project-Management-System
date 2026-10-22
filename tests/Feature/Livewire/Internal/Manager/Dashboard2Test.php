<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Dashboard2Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Dashboard2::class)
            ->assertStatus(200);
    }
}
