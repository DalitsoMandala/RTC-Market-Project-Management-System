<?php
namespace App\Livewire;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\Project;
use Illuminate\Support\Facades\File;
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
            'indicator_no'            => 'required|string|max:50',
            'indicator_name'          => 'required|string|max:255',
            'project_id'              => 'required|exists:projects,id',
            'selectedDisaggregations' => 'array',
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
            $oldIndicatorNo   = $indicator->indicator_no;   // Capture old number for file renaming
            $oldIndicatorName = $indicator->indicator_name; // Capture old name for file renaming
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

        // Handle physical file generation or renaming cross-platform
        $this->handleIndicatorFileClass($this->indicator_no, $oldIndicatorNo);

        $currentDisaggregations  = $indicator->disaggregations()->pluck('name')->sort()->values()->toArray();
        $selectedDisaggregations = collect($this->selectedDisaggregations)->sort()->values()->toArray();

        if ($currentDisaggregations !== $selectedDisaggregations) {
            $indicator->update([
                'disagg_backup' => json_encode($currentDisaggregations),
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
        $this->alert('success', 'Successfully updated');
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

        $newFileName = $newClassName . '.php';
        $newFilePath = $directory . '/' . $newFileName;

        // Repository-relative paths for reliable git execution
        $relativeNewFile = 'app/Helpers/rtc_market/indicators/' . $newFileName;

        // If updating and the indicator number changed, rename file and update internal class name
        if ($formattedOldNo && $formattedOldNo !== $formattedNewNo) {
            $oldFilePath     = $directory . '/' . $oldClassName . '.php';
            $relativeOldFile = 'app/Helpers/rtc_market/indicators/' . $oldClassName . '.php';

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
            $stub = "<?php\n\nnamespace App\Helpers\rtc_market\indicators;\n\nclass " . $newClassName . "\n{\n    // Automatically generated for indicator {$newNo}\n}\n";
            File::put($newFilePath, $stub);
        }

        // Automatically stage, commit, and push changes via Git
        try {
            $basePath = base_path();

            // 1. Ensure local git user is configured for the web server process
            \Illuminate\Support\Facades\Process::path($basePath)->run(['git', 'config', 'user.name', 'System Automation']);
            \Illuminate\Support\Facades\Process::path($basePath)->run(['git', 'config', 'user.email', 'system@production.local']);

            // 2. Stage the new or modified file using a relative path
            $addResult = \Illuminate\Support\Facades\Process::path($basePath)->run(['git', 'add', $relativeNewFile]);
            if (! $addResult->successful()) {
                \Illuminate\Support\Facades\Log::error('Git add failed: ' . $addResult->errorOutput());
            }

            // 3. If an old file existed and was renamed, stage the old file removal/change too
            if (isset($relativeOldFile) && File::exists($directory . '/' . $oldClassName . '.php')) {
                \Illuminate\Support\Facades\Process::path($basePath)->run(['git', 'add', $relativeOldFile]);
            }

            // 4. Commit the changes
            $commitResult = \Illuminate\Support\Facades\Process::path($basePath)->run([
                'git', 'commit', '-m', "chore: update indicator class file for {$newNo}",
            ]);

            if (! $commitResult->successful()) {
                \Illuminate\Support\Facades\Log::error('Git commit failed: ' . $commitResult->errorOutput());
            } else {
                // 5. Push changes to the remote repository safely using HEAD
                $pushResult = \Illuminate\Support\Facades\Process::path($basePath)->run(['git', 'push', 'origin', 'HEAD']);
                if (! $pushResult->successful()) {
                    \Illuminate\Support\Facades\Log::error('Git push failed: ' . $pushResult->errorOutput());
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Git automation exception: ' . $e->getMessage());
        }

        $this->fileLocation = "App\\Helpers\\rtc_market\\indicators\\" . $newClassName;

        Indicator::findOrFail($this->indicatorId)->class()->update([
            'class' => $this->fileLocation,
        ]);
    }
    // protected function handleIndicatorFileClass(string $newNo, ?string $oldNo = null): void
    // {
    //     $directory = app_path('Helpers/rtc_market/indicators');

    //     // Ensure directory exists cross-platform
    //     if (! File::exists($directory)) {
    //         File::makeDirectory($directory, 0755, true);
    //     }

    //     // Replace dots with underscores for valid PHP class and file names
    //     $formattedNewNo = str_replace('.', '_', $newNo);
    //     $formattedOldNo = $oldNo ? str_replace('.', '_', $oldNo) : null;

    //     $oldClassName = 'indicator_' . $formattedOldNo;
    //     $newClassName = 'indicator_' . $formattedNewNo;

    //     $newFileName = $newClassName . '.php';
    //     $newFilePath = $directory . '/' . $newFileName;

    //     // If updating and the indicator number changed, rename file and update internal class name
    //     if ($formattedOldNo && $formattedOldNo !== $formattedNewNo) {
    //         $oldFilePath = $directory . '/' . $oldClassName . '.php';

    //         if (File::exists($oldFilePath)) {
    //             // 1. Rename the physical file
    //             File::move($oldFilePath, $newFilePath);

    //             // 2. Read the file contents and update the class definition name
    //             $content        = File::get($newFilePath);
    //             $updatedContent = str_replace(
    //                 "class {$oldClassName}",
    //                 "class {$newClassName}",
    //                 $content
    //             );

    //             // 3. Write back the updated PHP content
    //             File::put($newFilePath, $updatedContent);
    //         }
    //     }

    //     // If the file still doesn't exist (brand new or old file wasn't found), create from stub
    //     if (! File::exists($newFilePath)) {
    //         $stub = "<?php\n\nnamespace App\Helpers\rtc_market\indicators;\n\nclass " . $newClassName . "\n{\n    // Automatically generated for indicator {$newNo}\n}\n";
    //         File::put($newFilePath, $stub);
    //     }

    //     $this->fileLocation = "App\\Helpers\\rtc_market\\indicators\\" . $newClassName;

    //     Indicator::findOrFail($this->indicatorId)->class()->update([
    //         'class' => $this->fileLocation,
    //     ]);
    // }

    public function checkIfFileLocationExists(): bool
    {
        if (! $this->indicator_no) {
            return false;
        }

        $formattedIndicatorNo = str_replace('.', '_', $this->indicator_no);
        $filePath             = app_path('Helpers/rtc_market/indicators/indicator_' . $formattedIndicatorNo . '.php');

        if (! File::exists($filePath)) {
            $this->alert('warning', 'Physical class file for indicator ' . $this->indicator_no . ' was missing and has been recreated.');
            // Automatically regenerate if missing on check
            $this->handleIndicatorFileClass($this->indicator_no);
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

        // Optional: Remove physical file on delete if desired
        // $filePath = app_path('Helpers/rtc_market/indicators/indicator_' . $indicator->indicator_no . '.php');
        // if (File::exists($filePath)) {
        //     File::delete($filePath);
        // }

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
