<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator235;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator235Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator235::class)
            ->assertStatus(200);
    }
}
