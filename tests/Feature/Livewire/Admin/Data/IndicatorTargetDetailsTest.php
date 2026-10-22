<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorTargetDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorTargetDetailsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorTargetDetails::class)
            ->assertStatus(200);
    }
}
