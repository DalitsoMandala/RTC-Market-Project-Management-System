<?php
namespace App\Livewire;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class IndicatorForm extends Component
{
    use LivewireAlert;

    // Null when creating, set when editing
    public ?int $indicatorId = null;

    public string $indicator_no   = '';
    public string $indicator_name = '';
    public $project_id            = '';

    public array $selectedLeadPartner     = [];
    public array $selectedSource          = [];
    public array $selectedDisaggregations = [];

    public $projects              = [];
    public $leadPartners          = [];
    public $sources               = [];
    public $disaggregationOptions = [];
    public $file_exists           = false;
    public $deleteConfirm         = '';
    // Populated after save() so the modal can show where the indicator's files live
    public ?string $fileLocation = null;

    public function mount(): void
    {
        $this->projects     = Project::select('id', 'name')->get();
        $this->leadPartners = Organisation::select('id', 'name')->get();

        $this->disaggregationOptions = IndicatorDisaggregation::selectRaw('MIN(id) as id, name')
            ->groupBy('name')
            ->orderBy('name')
            ->get();
    }

    #[On('openIndicatorModal')]
    public function openModal($indicatorId = null): void
    {
        $this->resetValidation();
        $this->reset([
            'indicator_no',
            'indicator_name',
            'project_id',
            'selectedLeadPartner',
            'selectedSource',
            'selectedDisaggregations',
            'fileLocation',
        ]);

        $this->indicatorId = $indicatorId;

        if ($indicatorId) {
            $indicator = Indicator::with(['responsiblePeopleforIndicators', 'forms', 'disaggregations'])
                ->findOrFail($indicatorId);

            $this->indicator_no   = $indicator->indicator_no;
            $this->indicator_name = $indicator->indicator_name;
            $this->project_id     = $indicator->project_id;

            // Set file location path dynamically
            $this->fileLocation = "App\\Helpers\\rtc_market\\indicators\\indicator_" . $this->indicator_no;

            // Check if file exists and notify if missing
            $this->file_exists = $this->checkIfFileLocationExists();

            $this->selectedDisaggregations = $indicator->disaggregations->pluck('name')->map(fn($id) => (string) $id)->toArray();
        }

        $this->dispatch('show-indicator-modal');

        $this->dispatch(
            'select-partners',
            data: $this->selectedLeadPartner,
            data2: $this->selectedSource,
            data3: $this->selectedDisaggregations,
        );
    }

    #[On('openDeleteIndicatorModal')]
    public function openDeleteModal($indicatorId): void
    {
        $this->resetValidation();
        $this->indicatorId    = $indicatorId;
        $indicator            = Indicator::findOrFail($indicatorId);
        $this->indicator_name = $indicator->indicator_name;
        $this->dispatch('show-delete-indicator-modal');
    }

    protected function rules(): array
    {
        return [
            'indicator_no'            => [
                'required',
                'string',
                'max:50',
                Rule::unique('indicators', 'indicator_no')->ignore($this->indicatorId),
            ],
            'indicator_name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('indicators', 'indicator_name')->ignore($this->indicatorId),
            ],
            'project_id'              => 'required|exists:projects,id',
            'selectedDisaggregations' => 'array|min:1',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $oldIndicatorNo   = null;
        $oldIndicatorName = null;
        $oldIndicatorId   = null;
        if ($this->indicatorId) {
            $indicator        = Indicator::findOrFail($this->indicatorId);
            $oldIndicatorNo   = $indicator->indicator_no;
            $oldIndicatorName = $indicator->indicator_name;
            $oldIndicatorId   = $indicator->id;
        } else {
            $indicator = new Indicator();
        }

        $indicator->indicator_no                                                   = $this->indicator_no;
        $indicator->indicator_name                                                 = $this->indicator_name;
        $indicator->previous_indicator_id ?? $indicator->previous_indicator_id     = $oldIndicatorId;
        $indicator->previous_indicator_no ?? $indicator->previous_indicator_no     = $oldIndicatorNo;
        $indicator->previous_indicator_name ?? $indicator->previous_indicator_name = $oldIndicatorName;
        $indicator->project_id                                                     = $this->project_id;
        $indicator->save();

        // Assign the new ID so handleIndicatorFileClass() knows which record to update/create
        $this->indicatorId = $indicator->id;

        // Handle physical file generation or renaming cross-platform with git & safety measures
        $this->handleIndicatorFileClass($this->indicator_no, $oldIndicatorNo);

        $currentDisaggregations  = $indicator->disaggregations()->pluck('name')->sort()->values()->toArray();
        $selectedDisaggregations = collect($this->selectedDisaggregations)->sort()->values()->toArray();

        if ($currentDisaggregations !== $selectedDisaggregations) {
            $indicator->update([
                'disagg_backup' => count($currentDisaggregations) > 0 ? json_encode($currentDisaggregations) : null,
            ]);

            $indicator->disaggregations()->delete();

            foreach ($this->selectedDisaggregations as $name) {
                $indicator->disaggregations()->create([
                    'name' => $name,
                ]);
            }
        }

        $this->selectedDisaggregations = $indicator->disaggregations->pluck('name')->map(fn($id) => (string) $id)->toArray();

        $this->dispatch('hideModal', name: 'indicator-crud-modal');
        $this->dispatch('refresh')->to(\App\Livewire\Tables\IndicatorTable::class);
        $this->alert('success', 'Successfully saved');
    }

    protected function handleIndicatorFileClass(string $newNo, ?string $oldNo = null): void
    {
        $directory = app_path('Helpers/rtc_market/indicators');

        // Ensure directory exists cross-platform
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Replace dots with underscores for valid PHP class and file names
        $formattedNewNo = str_replace('.', '_', $newNo);
        $formattedOldNo = $oldNo ? str_replace('.', '_', $oldNo) : null;

        $oldClassName = 'indicator_' . $formattedOldNo;
        $newClassName = 'indicator_' . $formattedNewNo;

        $oldFileName = $oldClassName . '.php';
        $newFileName = $newClassName . '.php';

        $oldFilePath = $directory . '/' . $oldFileName;
        $newFilePath = $directory . '/' . $newFileName;

        // If updating and the indicator number changed, rename file and update internal class name
        if ($formattedOldNo && $formattedOldNo !== $formattedNewNo) {
            if (File::exists($oldFilePath)) {
                // 1. Rename the physical file
                File::move($oldFilePath, $newFilePath);

                // 2. Read the file contents and update the class definition name
                $content        = File::get($newFilePath);
                $updatedContent = str_replace(
                    "class {$oldClassName}",
                    "class {$newClassName}",
                    $content
                );

                // 3. Write back the updated PHP content
                File::put($newFilePath, $updatedContent);
            }
        }

        // If the file still doesn't exist (brand new or old file wasn't found), create from stub
        if (! File::exists($newFilePath)) {
            $stub = "<?php\n\nnamespace App\\Helpers\\rtc_market\indicators;\n\nclass " . $newClassName . "\n{\n    // Automatically generated for indicator {$newNo}\n}\n";
            File::put($newFilePath, $stub);
        }

        $this->fileLocation = "App\\Helpers\\rtc_market\\indicators\\" . $newClassName;

        // Use updateOrCreate so it inserts if it doesn't exist (new indicator) or updates if it does
        Indicator::findOrFail($this->indicatorId)->class()->updateOrCreate(
            [],
            ['class' => $this->fileLocation]
        );

    }

    public function checkIfFileLocationExists(): bool
    {
        if (! $this->indicator_no) {
            return false;
        }

        $formattedIndicatorNo = str_replace('.', '_', $this->indicator_no);
        $filePath             = app_path('Helpers/rtc_market/indicators/indicator_' . $formattedIndicatorNo . '.php');

        if (! File::exists($filePath)) {
            $this->alert('warning', 'Physical class file for indicator ' . $this->indicator_no . ' was missing and has been recreated. Please make sure you commit the changes in your version control system.', [
                'timer' => 5000,
                'toast' => false,
            ]);
            // Automatically regenerate if missing on check
            $this->handleIndicatorFileClass($this->indicator_no);
            $this->fileLocation = "App\\Helpers\\rtc_market\\indicators\\indicator_" . $formattedIndicatorNo;
            $this->file_exists  = $this->checkIfFileLocationExists();
            return false;
        }

        return true;
    }

    #[On('deleteIndicator')]
    public function delete($indicatorId): void
    {
        $this->validate([
            'indicatorId'   => 'required|exists:indicators,id',
            'deleteConfirm' => 'required|in:DELETE',
        ]);
        $indicator = Indicator::findOrFail($indicatorId);

        $indicator->delete();

        $this->dispatch('refresh')->to(\App\Livewire\Tables\IndicatorTable::class);
        session()->flash('message', 'Indicator deleted.');
    }

    public function restoreDisaggregations(): void
    {
        $indicator = Indicator::findOrFail($this->indicatorId);
        if (! $indicator->disagg_backup) {
            $this->alert('error', 'No disaggregations to restore');
            return;
        }
        $disaggregations = json_decode($indicator->disagg_backup);
        $indicator->disaggregations()->delete();

        foreach ($disaggregations as $name) {
            $indicator->disaggregations()->create([
                'name' => $name,
            ]);
        }
        $indicator->update([
            'disagg_backup' => null,
        ]);
        session()->flash('success', 'Disaggregations restored.');
        $this->redirect(url()->previous());
    }

    public function render()
    {
        return view('livewire.indicator-form');
    }
}
