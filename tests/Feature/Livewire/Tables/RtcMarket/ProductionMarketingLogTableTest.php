<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProductionMarketingLogTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductionMarketingLogTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ProductionMarketingLogTable::class)
            ->assertStatus(200);
    }
}
