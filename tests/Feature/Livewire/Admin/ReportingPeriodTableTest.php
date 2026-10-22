<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingPeriodTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingPeriodTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingPeriodTable::class)
            ->assertStatus(200);
    }
}
