<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard3Charts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Dashboard3ChartsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Dashboard3Charts::class)
            ->assertStatus(200);
    }
}
