<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FinancialYearTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialYearTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(FinancialYearTable::class)
            ->assertStatus(200);
    }
}
