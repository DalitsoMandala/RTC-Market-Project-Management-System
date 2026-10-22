<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorB4;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorB4Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorB4::class)
            ->assertStatus(200);
    }
}
