<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ChartsView2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChartsView2Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ChartsView2::class)
            ->assertStatus(200);
    }
}
