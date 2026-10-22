<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator343;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator343Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator343::class)
            ->assertStatus(200);
    }
}
