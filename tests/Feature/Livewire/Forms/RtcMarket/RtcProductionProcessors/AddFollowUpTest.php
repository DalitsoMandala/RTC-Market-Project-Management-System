<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AddFollowUp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddFollowUpTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(AddFollowUp::class)
            ->assertStatus(200);
    }
}
