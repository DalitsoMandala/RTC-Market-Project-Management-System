<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator112;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator112Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator112::class)
            ->assertStatus(200);
    }
}
