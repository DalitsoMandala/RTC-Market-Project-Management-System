<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Add;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Add::class)
            ->assertStatus(200);
    }
}
