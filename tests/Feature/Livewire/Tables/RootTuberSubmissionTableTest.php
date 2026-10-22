<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RootTuberSubmissionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RootTuberSubmissionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RootTuberSubmissionTable::class)
            ->assertStatus(200);
    }
}
