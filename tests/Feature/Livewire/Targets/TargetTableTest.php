<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TargetTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TargetTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(TargetTable::class)
            ->assertStatus(200);
    }
}
