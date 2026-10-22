<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BaselineSingle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BaselineSingleTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(BaselineSingle::class)
            ->assertStatus(200);
    }
}
