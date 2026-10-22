<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorB5;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorB5Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorB5::class)
            ->assertStatus(200);
    }
}
