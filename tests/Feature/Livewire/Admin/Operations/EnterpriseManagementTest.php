<?php

namespace Tests\Feature\Livewire;

use App\Livewire\EnterpriseManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnterpriseManagementTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(EnterpriseManagement::class)
            ->assertStatus(200);
    }
}
