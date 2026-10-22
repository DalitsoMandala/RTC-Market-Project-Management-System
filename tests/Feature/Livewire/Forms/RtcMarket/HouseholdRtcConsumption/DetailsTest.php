<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Details;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DetailsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Details::class)
            ->assertStatus(200);
    }
}
