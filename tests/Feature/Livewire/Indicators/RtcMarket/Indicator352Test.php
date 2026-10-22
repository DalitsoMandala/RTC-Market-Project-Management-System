<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator352;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator352Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator352::class)
            ->assertStatus(200);
    }
}
