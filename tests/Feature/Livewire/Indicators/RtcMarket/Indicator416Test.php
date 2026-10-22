<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator416;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator416Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator416::class)
            ->assertStatus(200);
    }
}
