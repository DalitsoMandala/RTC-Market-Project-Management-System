<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ManualSubmissionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManualSubmissionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ManualSubmissionTable::class)
            ->assertStatus(200);
    }
}
