<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionProcessorInterMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionProcessorInterMarketsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionProcessorInterMarkets::class)
            ->assertStatus(200);
    }
}
