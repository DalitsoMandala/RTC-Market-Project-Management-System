<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmProcessorAggCenters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmProcessorAggCentersTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmProcessorAggCenters::class)
            ->assertStatus(200);
    }
}
