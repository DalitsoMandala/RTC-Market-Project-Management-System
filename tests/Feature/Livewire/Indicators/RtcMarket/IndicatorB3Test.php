<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorB3;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorB3Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorB3::class)
            ->assertStatus(200);
    }
}
