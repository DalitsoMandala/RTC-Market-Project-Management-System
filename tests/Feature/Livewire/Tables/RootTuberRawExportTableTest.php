<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RootTuberRawExportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RootTuberRawExportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RootTuberRawExportTable::class)
            ->assertStatus(200);
    }
}
