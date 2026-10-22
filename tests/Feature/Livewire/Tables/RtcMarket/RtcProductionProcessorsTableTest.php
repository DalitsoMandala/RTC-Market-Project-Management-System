<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionProcessorsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionProcessorsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionProcessorsTable::class)
            ->assertStatus(200);
    }
}
