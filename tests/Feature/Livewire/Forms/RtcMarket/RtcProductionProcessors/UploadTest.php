<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UploadTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(Upload::class)
            ->assertStatus(200);
    }
}
