<?php

namespace Tests\Feature\Livewire;

use App\Livewire\aggregatedReportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class aggregatedReportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(aggregatedReportTable::class)
            ->assertStatus(200);
    }
}
