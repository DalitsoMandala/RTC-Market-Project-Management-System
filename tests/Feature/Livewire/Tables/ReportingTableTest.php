<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingTable::class)
            ->assertStatus(200);
    }
}
