<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AssignedTargets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignedTargetsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(AssignedTargets::class)
            ->assertStatus(200);
    }
}
