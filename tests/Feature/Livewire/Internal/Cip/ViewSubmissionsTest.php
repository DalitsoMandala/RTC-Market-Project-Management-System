<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ViewSubmissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewSubmissionsTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ViewSubmissions::class)
            ->assertStatus(200);
    }
}
