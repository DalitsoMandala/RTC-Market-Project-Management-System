<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ChartsView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChartsViewTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ChartsView::class)
            ->assertStatus(200);
    }
}
