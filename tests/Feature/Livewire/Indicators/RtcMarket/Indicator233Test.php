<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator233;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator233Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator233::class)
            ->assertStatus(200);
    }
}
