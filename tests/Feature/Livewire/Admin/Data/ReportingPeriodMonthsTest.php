<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingPeriodMonths;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingPeriodMonthsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingPeriodMonths::class)
            ->assertStatus(200);
    }
}
