<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator353;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator353Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator353::class)
            ->assertStatus(200);
    }
}
