<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MarketDataTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketDataTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(MarketDataTable::class)
            ->assertStatus(200);
    }
}
