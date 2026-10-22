<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator331;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator331Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator331::class)
            ->assertStatus(200);
    }
}
