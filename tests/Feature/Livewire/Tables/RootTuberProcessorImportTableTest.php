<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RootTuberProcessorImportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RootTuberProcessorImportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RootTuberProcessorImportTable::class)
            ->assertStatus(200);
    }
}
