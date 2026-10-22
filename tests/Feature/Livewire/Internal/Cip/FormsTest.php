<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Forms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Forms::class)
            ->assertStatus(200);
    }
}
