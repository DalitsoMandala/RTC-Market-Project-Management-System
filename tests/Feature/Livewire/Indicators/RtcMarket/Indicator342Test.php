<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator342;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator342Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator342::class)
            ->assertStatus(200);
    }
}
