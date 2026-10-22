<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator355;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator355Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator355::class)
            ->assertStatus(200);
    }
}
