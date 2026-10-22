<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorDetailTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorDetailTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorDetailTable::class)
            ->assertStatus(200);
    }
}
