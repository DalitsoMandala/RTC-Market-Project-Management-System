<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmFarmerBasic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmFarmerBasicTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmFarmerBasic::class)
            ->assertStatus(200);
    }
}
