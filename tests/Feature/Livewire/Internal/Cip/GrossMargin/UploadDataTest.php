<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UploadData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UploadDataTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UploadData::class)
            ->assertStatus(200);
    }
}
