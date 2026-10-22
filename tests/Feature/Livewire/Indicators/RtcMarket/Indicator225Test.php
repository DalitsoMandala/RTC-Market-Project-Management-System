<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator225;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator225Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator225::class)
            ->assertStatus(200);
    }
}
