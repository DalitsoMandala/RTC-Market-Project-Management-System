<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorB2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorB2Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorB2::class)
            ->assertStatus(200);
    }
}
