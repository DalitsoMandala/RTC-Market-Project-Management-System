<?php

namespace Tests\Feature\Livewire;

use App\Livewire\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(View::class)
            ->assertStatus(200);
    }
}
