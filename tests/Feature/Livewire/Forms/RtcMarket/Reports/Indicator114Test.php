<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator114;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator114Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator114::class)
            ->assertStatus(200);
    }
}
