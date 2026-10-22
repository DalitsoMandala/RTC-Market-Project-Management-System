<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DatabaseBackupTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DatabaseBackupTableTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(DatabaseBackupTable::class)
            ->assertStatus(200);
    }
}
