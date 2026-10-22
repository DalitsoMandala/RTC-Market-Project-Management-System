<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorTargets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorTargetsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorTargets::class)
            ->assertStatus(200);
    }
}
