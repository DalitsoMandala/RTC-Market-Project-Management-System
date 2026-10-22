<?php

namespace Tests\Feature\Livewire;

use App\Livewire\OrganisationFormsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationFormsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(OrganisationFormsTable::class)
            ->assertStatus(200);
    }
}
