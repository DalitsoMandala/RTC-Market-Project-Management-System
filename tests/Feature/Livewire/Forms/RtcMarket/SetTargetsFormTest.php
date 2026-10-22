<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SetTargetsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SetTargetsFormTest extends TestCase
{
    /** @test */
    public function it_can_render_component()
    {
        $this->withoutVite();

        Livewire::test(SetTargetsForm::class)
            ->assertStatus(200);
    }
}
