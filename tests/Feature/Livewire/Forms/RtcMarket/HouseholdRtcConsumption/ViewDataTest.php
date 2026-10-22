<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ViewData::class)
            ->assertStatus(200);
    }
}
