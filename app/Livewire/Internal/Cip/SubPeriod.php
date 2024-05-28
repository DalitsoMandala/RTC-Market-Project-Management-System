<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SubPeriod extends Component
{
    use LivewireAlert;

    public $rowId;
    public $forms;

    public $status = true;
    #[Validate('required')]
    public $start_period;
    #[Validate('required')]
    public $end_period;
    #[Validate('required')]
    public $Selected;
    public $expired;
    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        $this->validate();
        $this->resetErrorBag();
        try {

            if ($this->rowId) {

                SubmissionPeriod::find($this->rowId)->update([
                    'date_established' => $this->start_period,
                    'date_ending' => $this->end_period,
                    'is_open' => $this->expired === true ? false : $this->status,
                    'form_id' => $this->Selected,
                    'is_expired' => $this->expired,
                ]);
                session()->flash('success', 'Updated Successfully');

            } else {

                $find = SubmissionPeriod::where('form_id', $this->Selected)->where('is_open', true)->first();

                if ($find) {

                    session()->flash('error', 'This record already exists see the table below!');

                } else {

                    SubmissionPeriod::create([
                        'date_established' => $this->start_period,
                        'date_ending' => $this->end_period,
                        'is_open' => $this->status,
                        'form_id' => $this->Selected,
                    ]);
                    session()->flash('success', 'Created Successfully');

                }

            }

            $this->dispatch('refresh');

        } catch (\Throwable $th) {
            session()->flash('error', 'something went wrong');

        }
        $this->reset();
        $this->loadData();
    }

    public function loadData()
    {
        // $form = Form::leftJoin('submission_periods', 'submission_periods.form_id', 'forms.id')->select(['forms.*'])->get();
        //  $OpenedForms = SubmissionPeriod::pluck('form_id')->toArray();

        // $this->forms = $form->whereNotIn('id', $OpenedForms) ?? [];

        $this->forms = Form::all();

    }
    #[On('editData')]
    public function fillData($rowId)
    {

        $this->rowId = $rowId;

        $this->start_period = Carbon::parse(SubmissionPeriod::find($rowId)->date_established)->format('Y-m-d');
        $this->end_period = Carbon::parse(SubmissionPeriod::find($rowId)->date_ending)->format('Y-m-d');
        $this->status = SubmissionPeriod::find($rowId)->is_open === 1 ? true : false;
        $this->Selected = SubmissionPeriod::find($rowId)->form_id;
    }

    public function mount()
    {
        $this->loadData();

    }
    public function render()
    {

        return view('livewire.internal.cip.sub-period', [

        ]);
    }
}