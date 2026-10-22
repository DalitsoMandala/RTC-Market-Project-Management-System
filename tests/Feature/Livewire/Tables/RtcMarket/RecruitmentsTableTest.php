<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RecruitmentsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecruitmentsTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(RecruitmentsTable::class)
            ->assertStatus(200);
    }
}
