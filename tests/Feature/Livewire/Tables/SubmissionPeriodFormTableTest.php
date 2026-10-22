<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionPeriodFormTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionPeriodFormTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmissionPeriodFormTable::class)
            ->assertStatus(200);
    }
}
