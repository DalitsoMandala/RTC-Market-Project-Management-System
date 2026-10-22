<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmProcessorMIS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmProcessorMISTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmProcessorMIS::class)
            ->assertStatus(200);
    }
}
