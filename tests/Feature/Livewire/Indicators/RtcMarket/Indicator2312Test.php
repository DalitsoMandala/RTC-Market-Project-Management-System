<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Indicator2312;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Indicator2312Test extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Indicator2312::class)
            ->assertStatus(200);
    }
}
