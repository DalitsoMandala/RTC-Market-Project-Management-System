<?php

namespace Tests\Feature\Livewire;

use App\Livewire\NumberIndicators;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NumberIndicatorsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(NumberIndicators::class)
            ->assertStatus(200);
    }
}
