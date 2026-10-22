<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RpmFarmerMIS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RpmFarmerMISTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RpmFarmerMIS::class)
            ->assertStatus(200);
    }
}
