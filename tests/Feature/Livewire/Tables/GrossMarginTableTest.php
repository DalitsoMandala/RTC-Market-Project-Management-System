<?php

namespace Tests\Feature\Livewire;

use App\Livewire\GrossMarginTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrossMarginTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(GrossMarginTable::class)
            ->assertStatus(200);
    }
}
