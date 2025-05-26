<?php

namespace App\Traits;

trait reportDefaultValuesTrait
{
    //

    public $form_id;
    public $indicator_id;
    public $financial_year_id;
    public $month_period_id;
    public $submission_period_id;
    public function mount()
    {
        $this->submissionPeriodId = $this->submission_period_id;
        $this->selectedForm = $this->form_id;
        $this->selectedIndicator = $this->indicator_id;
        $this->selectedFinancialYear = $this->financial_year_id;
    }
}
