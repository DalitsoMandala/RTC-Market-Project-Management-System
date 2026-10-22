<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AddGrossCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddGrossCategoryTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(AddGrossCategory::class)
            ->assertStatus(200);
    }
}
