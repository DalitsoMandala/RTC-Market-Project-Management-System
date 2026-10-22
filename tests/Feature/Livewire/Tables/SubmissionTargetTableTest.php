<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionTargetTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionTargetTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmissionTargetTable::class)
            ->assertStatus(200);
    }
}
