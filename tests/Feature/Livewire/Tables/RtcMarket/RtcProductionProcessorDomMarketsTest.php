<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionProcessorDomMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionProcessorDomMarketsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionProcessorDomMarkets::class)
            ->assertStatus(200);
    }
}
