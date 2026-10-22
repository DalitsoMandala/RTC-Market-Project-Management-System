<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MarketDataSubmissionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketDataSubmissionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(MarketDataSubmissionTable::class)
            ->assertStatus(200);
    }
}
