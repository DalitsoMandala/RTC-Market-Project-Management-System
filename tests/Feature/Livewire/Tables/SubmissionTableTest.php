<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmissionTable::class)
            ->assertStatus(200);
    }
}
