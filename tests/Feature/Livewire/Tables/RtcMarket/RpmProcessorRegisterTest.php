<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmProcessorRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmProcessorRegisterTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmProcessorRegister::class)
            ->assertStatus(200);
    }
}
