<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmitData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmitDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubmitData::class)
            ->assertStatus(200);
    }
}
