<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Edit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Edit::class)
            ->assertStatus(200);
    }
}
