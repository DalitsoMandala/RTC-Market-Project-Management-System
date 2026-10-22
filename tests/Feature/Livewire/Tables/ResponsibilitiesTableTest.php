<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ResponsibilitiesTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResponsibilitiesTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ResponsibilitiesTable::class)
            ->assertStatus(200);
    }
}
