<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingPeriodTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingPeriod::class)
            ->assertStatus(200);
    }
}
