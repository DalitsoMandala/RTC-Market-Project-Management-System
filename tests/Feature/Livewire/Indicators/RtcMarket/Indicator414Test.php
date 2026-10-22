<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator414;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator414Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator414::class)
            ->assertStatus(200);
    }
}
