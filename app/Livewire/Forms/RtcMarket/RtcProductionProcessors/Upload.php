<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exceptions\UserErrorException;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;
use App\Helpers\SheetNamesValidator;

use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\BatchDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;

    #[Validate('required')]
    public $upload;
    public $variable;
    public $rowId;
    public $selectedIndicator;
    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $selectedForm;

    public $submissionPeriodId;

    public $openSubmission = false;


    public function save()
    {


    }


    public function submitUpload()
    {

        $this->validate();
        try {
            //code...

            $userId = auth()->user()->id;

            if ($this->upload) {

                $name = 'hrc_' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs(path: 'imports', name: $name);
                $path = storage_path('app/imports/' . $name);
                $sheets = SheetNamesValidator::getSheetNames($path);

                try {


                    Excel::import(new RpmProcessorImport($userId, $sheets, $this->upload), $this->upload);

                    $uuid = session()->get('uuid');
                    $batch_data = session()->get('batch_data');
                    if (empty($batch_data['main'])) {
                        throw new UserErrorException("Your file has empty rows!");

                    }
                    $currentUser = Auth::user();
                    $table = ['rtc_production_processors', 'rpm_processor_follow_ups', 'rpm_processor_conc_agreements', 'rpm_processor_dom_markets', 'rpm_processor_inter_markets'];
                    if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                        $checkSubmission = Submission::where('period_id', $this->submissionPeriodId)
                            ->where('batch_type', 'batch')
                            ->where('user_id', auth()->user()->id)->first();
                        if ($checkSubmission) {

                            $this->reset('upload');
                            $this->dispatch('removeUploadedFile');

                            session()->flash('error', 'You have already submitted your batch data for this period!');
                        } else {

                            $submission = Submission::create([
                                'batch_no' => $uuid,
                                'form_id' => $this->selectedForm,
                                'user_id' => $currentUser->id,
                                'status' => 'approved',
                                'data' => json_encode($batch_data),
                                'batch_type' => 'batch',
                                'period_id' => $this->submissionPeriodId,
                                'table_name' => json_encode($table),
                                'is_complete' => 1,
                            ]);

                            $data = json_decode($submission->data, true);
                            $idMappings = [];
                            $highestId = RtcProductionProcessor::max('id');
                            foreach ($data['main'] as $mainSheet) {
                                $highestId++;
                                $mainSheet['is_registered'] = $mainSheet['is_registered'] == 'YES' ? true : false;
                                $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] == 'YES' ? true : false;
                                $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] == 'YES' ? true : false;
                                $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] == 'YES' ? true : false;
                                $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] == 'YES' ? true : false;
                                $idMappings[$mainSheet['#']] = $highestId;
                                unset($mainSheet['#']);
                                RtcProductionProcessor::create($mainSheet);

                            }

                            foreach ($data['followup'] as $mainSheet) {
                                $newId = $idMappings[$mainSheet['rpm_processor_id']];
                                $mainSheet['rpm_processor_id'] = $newId;

                                $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] == 'YES' ? true : false;
                                $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] == 'YES' ? true : false;
                                $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] == 'YES' ? true : false;
                                $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] == 'YES' ? true : false;
                                $mainTable = RpmProcessorFollowUp::create($mainSheet);

                                // follow up data

                            }

                            foreach ($data['agreement'] as $mainSheet) {
                                $newId = $idMappings[$mainSheet['rpm_processor_id']];
                                $mainSheet['rpm_processor_id'] = $newId;
                                $mainTable = RpmProcessorConcAgreement::create($mainSheet);

                                // conc agreement

                            }

                            foreach ($data['market'] as $mainSheet) {
                                $newId = $idMappings[$mainSheet['rpm_processor_id']];
                                $mainSheet['rpm_processor_id'] = $newId;
                                $mainTable = RpmProcessorDomMarket::create($mainSheet);

                                // dom market

                            }

                            foreach ($data['intermarket'] as $mainSheet) {
                                $newId = $idMappings[$mainSheet['rpm_processor_id']];
                                $mainSheet['rpm_processor_id'] = $newId;
                                $mainTable = RpmProcessorInterMarket::create($mainSheet);



                            }

                            // $link = 'cip/forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                            //   $currentUser->notify(new BatchDataAddedNotification($uuid, $link));


                            session()->flash('success', 'Successfully submitted!');
                            $this->redirect(route('cip-internal-submissions') . '#batch-submission');
                        }

                    } else if ($currentUser->hasAnyRole('external')) {

                        $checkSubmission = Submission::where('period_id', $this->submissionPeriodId)
                            ->where('batch_type', 'batch')
                            ->where('user_id', auth()->user()->id)->first();
                        if ($checkSubmission) {


                            $this->dispatch('removeUploadedFile');
                            session()->flash('error', 'You have already submitted your batch data!');
                        } else {
                            Submission::create([
                                'batch_no' => $uuid,
                                'form_id' => $this->selectedForm,
                                'period_id' => $this->submissionPeriodId,
                                'user_id' => $currentUser->id,
                                'data' => json_encode($batch_data),
                                'batch_type' => 'batch',
                                'table_name' => json_encode($table),
                            ]);
                            //     $link = 'external/forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                            //     $currentUser->notify(new BatchDataAddedNotification($uuid, $link));


                            session()->flash('success', 'Successfully submitted!');
                            $this->redirect(route('external-submissions') . '#batch-submission');

                        }
                    }

                } catch (UserErrorException $e) {


                    $this->reset('upload');
                    $this->dispatch('removeUploadedFile');

                    session()->flash('error', $e->getMessage());
                }

            }

        } catch (\Exception $th) {
            //throw $th;
            dd($th);
            session()->flash('error', 'Something went wrong!');
            Log::channel('system_log')->error($th);

        }

        $this->removeTemporaryFile();

    }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);

        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);

        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();

            if ($submissionPeriod) {

                $this->openSubmission = true;
                $user = Auth::user();
                $organisation = $user->organisation;

                $getSubmissionType = ResponsiblePerson::where('indicator_id', $this->selectedIndicator)->where('organisation_id', $organisation->id)->first();
                if ($getSubmissionType) {
                    $this->showReport = $getSubmissionType->type_of_submission == 'normal' ? false : true;
                }
            } else {
                $this->openSubmission = false;
            }
        }

    }



    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new RtcProductionProcessorWookbookExport, 'rtc_production_marketing_processors' . $time . '.xlsx');

    }

    public function removeTemporaryFile()
    {
        // Get the temporary file path
        if ($this->upload) {
            $temporaryFilePath = $this->upload->getRealPath();

            // Check if the file exists and delete it
            if (file_exists($temporaryFilePath)) {
                try {
                    unlink($temporaryFilePath);
                } catch (\Exception $e) {
                    // Handle the exception (e.g., log the error)
                    \Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }

        }

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-processors.upload');
    }
}
