<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SeedBeneficiaryTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeedBeneficiaryTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SeedBeneficiaryTable::class)
            ->assertStatus(200);
    }
}
