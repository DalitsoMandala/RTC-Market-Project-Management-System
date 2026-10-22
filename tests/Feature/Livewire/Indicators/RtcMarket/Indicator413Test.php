<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator413;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator413Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator413::class)
            ->assertStatus(200);
    }
}
