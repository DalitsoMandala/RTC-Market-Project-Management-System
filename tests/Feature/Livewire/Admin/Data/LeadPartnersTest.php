<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LeadPartners;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadPartnersTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(LeadPartners::class)
            ->assertStatus(200);
    }
}
