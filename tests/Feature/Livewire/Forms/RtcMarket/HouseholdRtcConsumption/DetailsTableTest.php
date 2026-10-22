<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DetailsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DetailsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(DetailsTable::class)
            ->assertStatus(200);
    }
}
