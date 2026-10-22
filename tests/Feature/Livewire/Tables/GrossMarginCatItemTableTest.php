<?php

namespace Tests\Feature\Livewire;

use App\Livewire\GrossMarginCatItemTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrossMarginCatItemTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(GrossMarginCatItemTable::class)
            ->assertStatus(200);
    }
}
