<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmFarmerMarketSegment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmFarmerMarketSegmentTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmFarmerMarketSegment::class)
            ->assertStatus(200);
    }
}
