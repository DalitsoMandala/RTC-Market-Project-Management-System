<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ChartsView3;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChartsView3Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ChartsView3::class)
            ->assertStatus(200);
    }
}
