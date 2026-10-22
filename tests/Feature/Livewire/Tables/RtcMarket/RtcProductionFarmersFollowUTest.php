<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionFarmersFollowU;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionFarmersFollowUTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionFarmersFollowU::class)
            ->assertStatus(200);
    }
}
