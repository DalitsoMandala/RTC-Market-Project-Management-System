<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorB6;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorB6Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorB6::class)
            ->assertStatus(200);
    }
}
