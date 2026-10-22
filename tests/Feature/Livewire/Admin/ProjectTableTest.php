<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ProjectTable::class)
            ->assertStatus(200);
    }
}
