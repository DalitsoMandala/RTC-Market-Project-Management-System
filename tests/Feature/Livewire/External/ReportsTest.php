<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Reports::class)
            ->assertStatus(200);
    }
}
