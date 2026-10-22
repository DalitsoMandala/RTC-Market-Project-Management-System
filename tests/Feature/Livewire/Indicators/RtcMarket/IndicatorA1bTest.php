<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorA1b;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorA1bTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorA1b::class)
            ->assertStatus(200);
    }
}
