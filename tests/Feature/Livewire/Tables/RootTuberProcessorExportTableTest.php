<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RootTuberProcessorExportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RootTuberProcessorExportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RootTuberProcessorExportTable::class)
            ->assertStatus(200);
    }
}
