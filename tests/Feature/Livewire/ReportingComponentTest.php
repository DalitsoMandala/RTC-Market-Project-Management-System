<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReportingComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingComponentTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ReportingComponent::class)
            ->assertStatus(200);
    }
}
