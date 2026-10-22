<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator131;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator131Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator131::class)
            ->assertStatus(200);
    }
}
