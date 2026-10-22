<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator341;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator341Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator341::class)
            ->assertStatus(200);
    }
}
