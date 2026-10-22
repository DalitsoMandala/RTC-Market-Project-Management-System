<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SeedBeneficiariesCassavaTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeedBeneficiariesCassavaTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SeedBeneficiariesCassavaTable::class)
            ->assertStatus(200);
    }
}
