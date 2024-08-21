<div>
    <hr>
    <h5>Target Details</h5>

    @foreach ($targetDetails as $index => $detail)
        <div class="border p-3 mb-3" wire:key="target-detail-{{ $index }}">
            <div class="mb-3">
                <label for="targetDetails.{{ $index }}.name" class="form-label">Name</label>
                <input type="text" wire:model="targetDetails.{{ $index }}.name"
                    id="targetDetails.{{ $index }}.name" class="form-control" required>
                @error('targetDetails.' . $index . '.name')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="targetDetails.{{ $index }}.target_value" class="form-label">Target Value</label>
                <input type="text" wire:model="targetDetails.{{ $index }}.target_value"
                    id="targetDetails.{{ $index }}.target_value" class="form-control" required>
                @error('targetDetails.' . $index . '.target_value')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="targetDetails.{{ $index }}.type" class="form-label">Type</label>
                <select wire:model="targetDetails.{{ $index }}.type"
                    id="targetDetails.{{ $index }}.type" class="form-select" required>
                    <option value="number">Number</option>
                    <option value="percentage">Percentage</option>
                </select>
                @error('targetDetails.' . $index . '.type')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <button type="button" class="btn btn-danger"
                wire:click="removeTargetDetail({{ $index }})">Remove</button>
        </div>
    @endforeach

    <button type="button" class="btn btn-secondary mt-3" wire:click="addTargetDetail">Add Target Detail</button>
</div>
