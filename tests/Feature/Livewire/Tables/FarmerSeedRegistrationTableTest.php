<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FarmerSeedRegistrationTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FarmerSeedRegistrationTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(FarmerSeedRegistrationTable::class)
            ->assertStatus(200);
    }
}
