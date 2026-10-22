<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorTable::class)
            ->assertStatus(200);
    }
}
