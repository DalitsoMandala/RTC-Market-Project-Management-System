<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator234;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator234Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator234::class)
            ->assertStatus(200);
    }
}
