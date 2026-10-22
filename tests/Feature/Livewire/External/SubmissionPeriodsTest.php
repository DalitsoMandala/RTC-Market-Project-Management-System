<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionPeriods;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionPeriodsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmissionPeriods::class)
            ->assertStatus(200);
    }
}
