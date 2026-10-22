<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator211;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator211Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator211::class)
            ->assertStatus(200);
    }
}
