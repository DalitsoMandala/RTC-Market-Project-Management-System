<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorTargetDetailsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorTargetDetailsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorTargetDetailsTable::class)
            ->assertStatus(200);
    }
}
