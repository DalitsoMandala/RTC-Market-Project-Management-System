<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingMonthsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingMonthsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingMonthsTable::class)
            ->assertStatus(200);
    }
}
