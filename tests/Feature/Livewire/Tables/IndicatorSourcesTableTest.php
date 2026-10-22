<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorSourcesTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorSourcesTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorSourcesTable::class)
            ->assertStatus(200);
    }
}
