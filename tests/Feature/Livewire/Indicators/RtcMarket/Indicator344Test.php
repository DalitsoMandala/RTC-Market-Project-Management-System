<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator344;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator344Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator344::class)
            ->assertStatus(200);
    }
}
