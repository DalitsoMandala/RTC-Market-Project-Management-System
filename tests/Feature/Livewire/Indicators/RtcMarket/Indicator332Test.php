<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator332;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator332Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator332::class)
            ->assertStatus(200);
    }
}
