<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RootTuberRawImportTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RootTuberRawImportTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RootTuberRawImportTable::class)
            ->assertStatus(200);
    }
}
