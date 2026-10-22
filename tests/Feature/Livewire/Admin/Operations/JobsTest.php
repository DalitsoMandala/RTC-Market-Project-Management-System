<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Jobs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Jobs::class)
            ->assertStatus(200);
    }
}
