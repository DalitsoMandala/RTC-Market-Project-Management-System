<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator231;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator231Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator231::class)
            ->assertStatus(200);
    }
}
