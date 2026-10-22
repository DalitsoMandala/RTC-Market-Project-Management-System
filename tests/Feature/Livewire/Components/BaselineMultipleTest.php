<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BaselineMultiple;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BaselineMultipleTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(BaselineMultiple::class)
            ->assertStatus(200);
    }
}
