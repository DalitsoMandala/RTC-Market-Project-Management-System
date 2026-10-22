<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ManageData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ManageData::class)
            ->assertStatus(200);
    }
}
