<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator1312;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator1312Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator1312::class)
            ->assertStatus(200);
    }
}
