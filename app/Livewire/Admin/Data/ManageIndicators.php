<?php

namespace App\Livewire\Admin\Data;

use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\IndicatorClass;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\File;

class ManageIndicators extends Component
{
    use LivewireAlert;

    public $disaggregations = [
        ['name' => ''],
    ]; // start with one input
    public $indicatorName;
    public $indicatorId;
    public $indicatorNumber;
    public $fileName;
    protected function rules()
    {
        return [
            'disaggregations' => 'array|min:1',
            'disaggregations.*.name' => 'required|string|max:255|distinct',
            'indicatorName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('indicators', 'indicator_name')->ignore($this->indicatorId),
            ],

            'indicatorNumber' => [
                'required',
                'string',
                'max:255',
                Rule::unique('indicators', 'indicator_no')->ignore($this->indicatorId),
            ],
            'fileName' => [
                Rule::requiredIf(function () {
                    // Return true if 'target_field' should be required
                    // i.e., both 'field_one' and 'field_two' are not empty
                    return !empty($this->fileName) && !empty($this->fileName);
                }),
                'max:255',

                Rule::unique('indicator_classes', 'class')->ignore($this->indicatorId),
            ],
        ];
    }

    protected function messages()
    {
        return [
            'disaggregations.*.name.required' => 'Disaggregation name is required',
        ];
    }

    public function addDisaggregation()
    {
        $this->disaggregations[] = ['name' => ''];
    }

    public function removeDisaggregation($index)
    {
        unset($this->disaggregations[$index]);
        $this->disaggregations = array_values($this->disaggregations);
    }


    public function saveIndicator()
    {
        try {
            $this->validate();


            // $newIndicator = new Indicator();
            // $newIndicator->indicator_name = $this->indicatorName;
            // $newIndicator->indicator_no = $this->indicatorNumber;
            // foreach ($this->disaggregations as $disaggregation) {
            //     $newDisaggregation = new IndicatorDisaggregation();
            //     $newDisaggregation->name = $disaggregation['name'];
            //     $newDisaggregation->indicator_id = $newIndicator->id;
            //     $newDisaggregation->save();
            // }
            // $newIndicator->class->class = $this->fileName;
            // $newIndicator->save();



            $this->dispatch('show-alert', data: [
                'type' => 'success',  // success, error, info, warning
                'message' => 'Successfully saved'
            ]);
        } catch (\illuminate\Validation\ValidationException $e) {
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        } catch (\Throwable $th) {
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'Something went wrong'
            ]);
            Log::error($th);
        }
    }
    public function checkFile()
    {


        $file = $this->fileName;

        // Normalize slashes
        $file = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);

        // Remove any leading "App\Helpers\rtc_market\indicators\"
        $prefix = 'App' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'rtc_market' . DIRECTORY_SEPARATOR . 'indicators' . DIRECTORY_SEPARATOR;
        if (str_starts_with($file, $prefix)) {
            $file = substr($file, strlen($prefix));
        }

        // Also allow if user pasted "Helpers/rtc_market/indicators/..."
        $prefix2 = 'Helpers' . DIRECTORY_SEPARATOR . 'rtc_market' . DIRECTORY_SEPARATOR . 'indicators' . DIRECTORY_SEPARATOR;
        if (str_starts_with($file, $prefix2)) {
            $file = substr($file, strlen($prefix2));
        }

        // Remove ".php" if they included it
        $file = preg_replace('/\.php$/i', '', $file);

        // Now use your normal approach
        $path = app_path("Helpers/rtc_market/indicators/{$file}.php");

        if (File::exists($path)) {
            // file exists
            dd('yes');
        }
    }
    public function checkIndicatorClass($indicatorName, $indicatorNumber, $fileName)
    {
        $checkIndicatorName = Indicator::where('indicator_name', $indicatorName)->exists();
        if ($checkIndicatorName) {
            return 'Indicator name already exists';
        }

        $checkIndicatorNumber = Indicator::where('indicator_no', $indicatorNumber)->exists();
        if ($checkIndicatorNumber) {
            return 'Indicator number already exists';
        }
    }

    public function mount() {}


    public function render()
    {
        return view('livewire.admin.data.manage-indicators');
    }
}
