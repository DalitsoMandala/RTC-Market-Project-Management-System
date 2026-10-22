<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Assignments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignmentsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Assignments::class)
            ->assertStatus(200);
    }
}
