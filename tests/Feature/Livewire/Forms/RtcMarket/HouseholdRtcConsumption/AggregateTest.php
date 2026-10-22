<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Aggregate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AggregateTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Aggregate::class)
            ->assertStatus(200);
    }
}
