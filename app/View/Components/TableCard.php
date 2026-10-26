
<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableCard extends Component
{
    public function __construct(
        public string $title,
        public ?string $subtitle = null,
        public ?string $url = null,
        public string $actionLabel = 'View More',
    ) {}

    public function render(): View
    {
        return view('components.table-card');
    }
}
