<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator224;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator224Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator224::class)
            ->assertStatus(200);
    }
}
