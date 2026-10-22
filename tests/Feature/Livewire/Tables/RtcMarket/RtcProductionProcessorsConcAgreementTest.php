<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionProcessorsConcAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionProcessorsConcAgreementTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionProcessorsConcAgreement::class)
            ->assertStatus(200);
    }
}
