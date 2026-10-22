<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BaselineTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BaselineTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(BaselineTable::class)
            ->assertStatus(200);
    }
}
