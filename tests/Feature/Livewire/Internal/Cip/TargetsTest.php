<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Targets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TargetsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Targets::class)
            ->assertStatus(200);
    }
}
