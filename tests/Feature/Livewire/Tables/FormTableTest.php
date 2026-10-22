<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FormTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(FormTable::class)
            ->assertStatus(200);
    }
}
