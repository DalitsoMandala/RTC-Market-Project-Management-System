<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ViewIndicators;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewIndicatorsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ViewIndicators::class)
            ->assertStatus(200);
    }
}
