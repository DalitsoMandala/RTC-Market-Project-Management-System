<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AssignedTargetsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignedTargetsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(AssignedTargetsTable::class)
            ->assertStatus(200);
    }
}
