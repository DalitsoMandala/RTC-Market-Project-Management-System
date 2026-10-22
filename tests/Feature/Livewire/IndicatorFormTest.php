<?php

namespace Tests\Feature\Livewire;

use App\Livewire\IndicatorForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorFormTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(IndicatorForm::class)
            ->assertStatus(200);
    }
}
