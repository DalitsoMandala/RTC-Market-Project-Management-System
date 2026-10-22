<?php

namespace Tests\Feature\Livewire;

use App\Livewire\OrganisationTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(OrganisationTable::class)
            ->assertStatus(200);
    }
}
