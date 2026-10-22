<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator232;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator232Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator232::class)
            ->assertStatus(200);
    }
}
