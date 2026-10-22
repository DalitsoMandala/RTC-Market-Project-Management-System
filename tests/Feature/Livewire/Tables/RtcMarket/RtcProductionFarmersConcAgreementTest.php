<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionFarmersConcAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionFarmersConcAgreementTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionFarmersConcAgreement::class)
            ->assertStatus(200);
    }
}
