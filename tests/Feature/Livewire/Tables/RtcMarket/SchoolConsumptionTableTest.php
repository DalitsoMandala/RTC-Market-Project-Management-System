<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SchoolConsumptionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolConsumptionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SchoolConsumptionTable::class)
            ->assertStatus(200);
    }
}
