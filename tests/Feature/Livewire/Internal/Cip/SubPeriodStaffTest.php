<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubPeriodStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubPeriodStaffTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SubPeriodStaff::class)
            ->assertStatus(200);
    }
}
