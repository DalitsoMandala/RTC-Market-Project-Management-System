<?php

namespace Tests\Feature\Livewire;

use App\Livewire\GrossMarginVarietyTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrossMarginVarietyTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(GrossMarginVarietyTable::class)
            ->assertStatus(200);
    }
}
