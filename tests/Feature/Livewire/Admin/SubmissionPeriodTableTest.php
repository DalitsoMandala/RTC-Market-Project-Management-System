<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionPeriodTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionPeriodTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmissionPeriodTable::class)
            ->assertStatus(200);
    }
}
