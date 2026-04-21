<?php

namespace App\Livewire\Admin\Operations;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Crop;
use App\Models\CropVariety;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
class EnterpriseManagement extends Component
{
       use WithPagination;
use LivewireAlert;
    // ── Filters ───────────────────────────────────────────────
    public string $search     = '';
    public string $typeFilter = '';

    // ── Add Crop ──────────────────────────────────────────────
    public string $newCropName  = '';
    public array  $newVarieties = [''];

    // ── Edit Crop ─────────────────────────────────────────────
    public ?int   $editCropId   = null;
    public string $editCropName = '';

    // ── Delete Crop ───────────────────────────────────────────
    public ?int   $deleteCropId   = null;
    public string $deleteCropName = '';

    // ── Variety Manager ───────────────────────────────────────
    public ?int   $activeCropId   = null;
    public string $activeCropName = '';
    public string $newVarietyName = '';

    // ── Edit Variety ──────────────────────────────────────────
    public ?int   $editVarietyId   = null;
    public string $editVarietyName = '';

    // ── Watchers ──────────────────────────────────────────────
    public function updatingSearch(): void     { $this->resetPage(); }
    public function updatingTypeFilter(): void { $this->resetPage(); }

    // ── Computed ──────────────────────────────────────────────
    #[Computed]
    public function crops()
    {
        return Crop::with('varieties')
            ->withCount('varieties')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhereHas('varieties', fn($q2) =>
                      $q2->where('name', 'like', "%{$this->search}%")
                  )
            )
            ->when($this->typeFilter === 'default', fn($q) => $q->where('is_default', true))
            ->when($this->typeFilter === 'custom',  fn($q) => $q->where('is_default', false))
            ->paginate(10);
    }

    #[Computed]
    public function activeVarieties()
    {
        if (! $this->activeCropId) return collect();
        return CropVariety::where('crop_id', $this->activeCropId)->get();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'     => Crop::count(),
            'varieties' => CropVariety::count(),
            'default'   => Crop::where('is_default', true)->count(),
            'custom'    => Crop::where('is_default', false)->count(),
        ];
    }

    // ── Add Crop ──────────────────────────────────────────────
    public function openAddModal(): void
    {
        $this->resetValidation();
        $this->newCropName  = '';
        $this->newVarieties = [''];
        $this->dispatch('showModal', name: 'add-crop-modal');
    }

    public function addVarietyField(): void
    {
        $this->newVarieties[] = '';
    }

    public function removeVarietyField(int $index): void
    {
        unset($this->newVarieties[$index]);
        $this->newVarieties = array_values($this->newVarieties);
    }

    public function saveCrop(): void
    {
        $this->validate([
            'newCropName' => 'required|string|max:100|unique:crops,name',
        ], [
            'newCropName.required' => 'Crop name is required.',
            'newCropName.unique'   => 'A crop with this name already exists.',
        ]);

        $crop = Crop::create(['name' => trim($this->newCropName)]);

        foreach ($this->newVarieties as $v) {
            if (filled(trim($v))) {
                CropVariety::create(['crop_id' => $crop->id, 'name' => trim($v)]);
            }
        }

        $this->dispatch('hideModal');

        $this->alert('success', "Crop \"{$crop->name}\" added successfully.");

        unset($this->crops, $this->stats);
    }

    // ── Edit Crop ─────────────────────────────────────────────
    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $crop               = Crop::findOrFail($id);
        $this->editCropId   = $id;
        $this->editCropName = $crop->name;
        $this->dispatch('showModal', name: 'edit-crop-modal');
    }

    public function updateCrop(): void
    {
        $this->validate([
            'editCropName' => "required|string|max:100|unique:crops,name,{$this->editCropId}",
        ], [
            'editCropName.required' => 'Crop name is required.',
            'editCropName.unique'   => 'A crop with this name already exists.',
        ]);

        $crop = Crop::findOrFail($this->editCropId);
        $old  = $crop->name;
        $crop->update(['name' => trim($this->editCropName)]);

        $this->dispatch('hideModal');
        $this->alert('warning', "Renamed \"{$old}\" to \"{$crop->name}\".");

        unset($this->crops);
    }

    // ── Delete Crop ───────────────────────────────────────────
    public function openDeleteModal(int $id): void
    {
        $crop                 = Crop::findOrFail($id);
        $this->deleteCropId   = $id;
        $this->deleteCropName = $crop->name;
        $this->dispatch('showModal', name: 'delete-crop-modal');
    }

    public function deleteCrop(): void
    {
        $crop = Crop::findOrFail($this->deleteCropId);
        $name = $crop->name;
        $crop->delete();

        $this->dispatch('hideModal');

        $this->alert('error', "Crop \"{$name}\" removed.");
        unset($this->crops, $this->stats);
    }

    // ── Variety Manager ───────────────────────────────────────
    public function openVarietyModal(int $cropId): void
    {
        $this->resetValidation();
        $crop                  = Crop::findOrFail($cropId);
        $this->activeCropId    = $cropId;
        $this->activeCropName  = $crop->name;
        $this->newVarietyName  = '';
        $this->editVarietyId   = null;
        $this->editVarietyName = '';
        $this->dispatch('showModal', name: 'variety-modal');
    }

    public function addVariety(): void
    {
        $this->validate([
            'newVarietyName' => 'required|string|max:100',
        ]);

        $name = strtolower(trim($this->newVarietyName));
        CropVariety::create(['crop_id' => $this->activeCropId, 'name' => $name]);
        $this->newVarietyName = '';
        $this->alert('success', "Variety \"{$name}\" added successfully.");
        unset($this->activeVarieties, $this->crops, $this->stats);
    }

    public function deleteVariety(int $id): void
    {
        $variety = CropVariety::findOrFail($id);
        $name    = $variety->name;
        $variety->delete();

        $this->alert('error', "Variety \"{$name}\" removed.");
        unset($this->activeVarieties, $this->crops, $this->stats);
    }

    public function openEditVariety(int $id): void
    {
        $this->resetValidation();
        $variety               = CropVariety::findOrFail($id);
        $this->editVarietyId   = $id;
        $this->editVarietyName = $variety->name;
    }

    public function updateVariety(): void
    {
        $this->validate([
            'editVarietyName' => 'required|string|max:100',
        ]);

        $variety = CropVariety::findOrFail($this->editVarietyId);
        $variety->update(['name' => trim($this->editVarietyName)]);
        $this->editVarietyId   = null;
        $this->editVarietyName = '';
     $this->alert('warning', "Variety \"{$variety->name}\" updated successfully.");
        unset($this->activeVarieties, $this->crops);
    }

    public function cancelEditVariety(): void
    {
        $this->editVarietyId   = null;
        $this->editVarietyName = '';
        $this->resetValidation();
    }


    public function render()
    {
        return view('livewire.admin.operations.enterprise-management');
    }
}
