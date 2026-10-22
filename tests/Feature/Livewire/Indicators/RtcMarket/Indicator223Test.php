<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator223;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator223Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator223::class)
            ->assertStatus(200);
    }
}
