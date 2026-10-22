<?php

namespace Tests\Feature\Livewire;

use App\Livewire\JobTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(JobTable::class)
            ->assertStatus(200);
    }
}
