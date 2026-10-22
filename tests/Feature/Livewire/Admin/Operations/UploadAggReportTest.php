<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UploadAggReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UploadAggReportTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(UploadAggReport::class)
            ->assertStatus(200);
    }
}
