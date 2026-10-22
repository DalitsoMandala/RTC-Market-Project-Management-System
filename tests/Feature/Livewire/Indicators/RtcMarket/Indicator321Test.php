<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator321;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator321Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator321::class)
            ->assertStatus(200);
    }
}
