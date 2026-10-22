<?php

namespace Tests\Feature\Livewire;

use App\Livewire\HouseholdRtcConsumptionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HouseholdRtcConsumptionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(HouseholdRtcConsumptionTable::class)
            ->assertStatus(200);
    }
}
