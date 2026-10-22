<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator345;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator345Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator345::class)
            ->assertStatus(200);
    }
}
