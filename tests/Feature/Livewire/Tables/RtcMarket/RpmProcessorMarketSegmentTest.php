<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmProcessorMarketSegment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmProcessorMarketSegmentTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmProcessorMarketSegment::class)
            ->assertStatus(200);
    }
}
