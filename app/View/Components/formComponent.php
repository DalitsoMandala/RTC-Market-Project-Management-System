<?php

namespace App\View\Components;

use Closure;
use Ramsey\Uuid\Uuid;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class formComponent extends Component
{
    public $title;
    public $pageTitle;
    public $formTitle;
    public $breadcrumbs;
    public $openSubmission;
    public $targetSet;
    public $targetIds;
    public $showTargetForm;
    public $hideSubmitButtons;
    public $skipDraftScript;
    public $formName;
    public $replaceUrl;
    public $showAlpineAlerts;
    public $formRoute;

    public function __construct(
        $title = null,
        $pageTitle = null,
        $formTitle = null,
        $breadcrumbs = null,
        $openSubmission = null,
        $targetSet = null,
        $targetIds = null,
        $showTargetForm = false,
        $hideSubmitButtons = false,
        $skipDraftScript = false,
        $formName = 'default',
        $showAlpineAlerts = false
    ) {
        $this->title = $title;
        $this->pageTitle = $pageTitle;
        $this->formTitle = $formTitle;
        $this->breadcrumbs = $breadcrumbs;
        $this->openSubmission = $openSubmission;
        $this->targetSet = $targetSet;
        $this->targetIds = $targetIds;
        $this->showTargetForm = $showTargetForm;
        $this->hideSubmitButtons = $hideSubmitButtons;
        $this->skipDraftScript = $skipDraftScript;
        $this->formName = $formName;
        $this->showAlpineAlerts = $showAlpineAlerts;



        $uuid = Uuid::uuid4()->toString();
        $currentUrl = url()->current();
        $this->replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

        $this->formRoute = strtolower(str_replace(' ', '-', $this->formName));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form-component');
    }
}
