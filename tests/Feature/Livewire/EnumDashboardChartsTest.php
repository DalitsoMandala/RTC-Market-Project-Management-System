<?php

namespace Tests\Feature\Livewire;

use App\Livewire\EnumDashboardCharts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnumDashboardChartsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(EnumDashboardCharts::class)
            ->assertStatus(200);
    }
}
