<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Projects;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Projects::class)
            ->assertStatus(200);
    }
}
