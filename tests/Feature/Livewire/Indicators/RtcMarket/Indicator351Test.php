<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator351;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator351Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator351::class)
            ->assertStatus(200);
    }
}
