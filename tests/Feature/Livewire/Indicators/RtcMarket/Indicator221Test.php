<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator221;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator221Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator221::class)
            ->assertStatus(200);
    }
}
