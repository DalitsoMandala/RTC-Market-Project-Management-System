<?php

namespace Tests\Feature\Livewire;

use App\Livewire\JobProgressTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobProgressTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(JobProgressTable::class)
            ->assertStatus(200);
    }
}
