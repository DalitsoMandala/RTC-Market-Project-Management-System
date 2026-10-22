<?php

namespace Tests\Feature\Livewire;

use App\Livewire\GrossMarginCategoryTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrossMarginCategoryTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(GrossMarginCategoryTable::class)
            ->assertStatus(200);
    }
}
