<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionFarmersDomMarkets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionFarmersDomMarketsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionFarmersDomMarkets::class)
            ->assertStatus(200);
    }
}
