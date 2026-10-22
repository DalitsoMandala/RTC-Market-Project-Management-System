<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator411;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator411Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator411::class)
            ->assertStatus(200);
    }
}
