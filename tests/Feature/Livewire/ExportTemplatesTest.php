<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ExportTemplates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExportTemplatesTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ExportTemplates::class)
            ->assertStatus(200);
    }
}
