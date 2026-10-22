<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator122;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator122Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator122::class)
            ->assertStatus(200);
    }
}
