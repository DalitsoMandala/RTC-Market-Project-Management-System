<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator311;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator311Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator311::class)
            ->assertStatus(200);
    }
}
