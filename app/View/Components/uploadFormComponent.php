<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class uploadFormComponent extends Component
{
    public $formName;
    public $targetSet;
    public $openSubmission;
    public $importing;
    public $importingFinished;
    public $progress;
    public $targetIds;
    public $selectedMonth;
    public $selectedFinancialYear;
    public $addDataRoute;
    public $currentRoute;
    public $breadcrumbs;
    public $pageTitle;
    public $formRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $formName,
        bool $targetSet = false,
        bool $openSubmission = true,
        bool $importing = false,
        bool $importingFinished = false,
        int $progress = 0,
        array $targetIds = [],
        $selectedMonth = null,
        $selectedFinancialYear = null,
        string $addDataRoute = '',
        string $currentRoute = '',
        $breadcrumbs = null,
        string $pageTitle = ''
    ) {
        $this->formName = $formName;
        $this->targetSet = $targetSet;
        $this->openSubmission = $openSubmission;
        $this->importing = $importing;
        $this->importingFinished = $importingFinished;
        $this->progress = $progress;
        $this->targetIds = $targetIds;
        $this->selectedMonth = $selectedMonth;
        $this->selectedFinancialYear = $selectedFinancialYear;
        $this->addDataRoute = $addDataRoute;
        $this->currentRoute = $currentRoute;
        $this->breadcrumbs = $breadcrumbs;
        $this->pageTitle = $pageTitle;

        $this->formRoute = strtolower(str_replace(' ', '-', $this->formName));
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.upload-form-component');
    }
}
