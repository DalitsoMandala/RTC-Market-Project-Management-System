<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmFarmerAggCenters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmFarmerAggCentersTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmFarmerAggCenters::class)
            ->assertStatus(200);
    }
}
