<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard2Charts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Dashboard2ChartsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Dashboard2Charts::class)
            ->assertStatus(200);
    }
}
