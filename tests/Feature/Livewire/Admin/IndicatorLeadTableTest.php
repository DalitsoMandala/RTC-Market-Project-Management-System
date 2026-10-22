<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorLeadTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorLeadTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorLeadTable::class)
            ->assertStatus(200);
    }
}
