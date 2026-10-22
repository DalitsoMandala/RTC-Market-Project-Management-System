<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Submissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Submissions::class)
            ->assertStatus(200);
    }
}
