<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UpdateBaselineData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UpdateBaselineDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UpdateBaselineData::class)
            ->assertStatus(200);
    }
}
