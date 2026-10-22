<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicators;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicators::class)
            ->assertStatus(200);
    }
}
