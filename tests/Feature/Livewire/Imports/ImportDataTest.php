<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ImportData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ImportDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(ImportData::class)
            ->assertStatus(200);
    }
}
