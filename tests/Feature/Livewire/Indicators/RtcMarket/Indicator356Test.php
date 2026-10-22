<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator356;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator356Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator356::class)
            ->assertStatus(200);
    }
}
