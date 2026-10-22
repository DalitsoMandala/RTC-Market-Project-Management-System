<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AdditionalReportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdditionalReportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(AdditionalReportTable::class)
            ->assertStatus(200);
    }
}
