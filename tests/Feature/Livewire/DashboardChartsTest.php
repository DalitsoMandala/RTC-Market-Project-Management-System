<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DashboardCharts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardChartsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(DashboardCharts::class)
            ->assertStatus(200);
    }
}
