<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubPeriodTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubPeriod::class)
            ->assertStatus(200);
    }
}
