<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RtcProductionFarmersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtcProductionFarmersTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RtcProductionFarmersTable::class)
            ->assertStatus(200);
    }
}
