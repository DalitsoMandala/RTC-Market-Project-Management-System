<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator354;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator354Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator354::class)
            ->assertStatus(200);
    }
}
