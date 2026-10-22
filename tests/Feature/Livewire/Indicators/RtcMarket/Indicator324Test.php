<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator324;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator324Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator324::class)
            ->assertStatus(200);
    }
}
