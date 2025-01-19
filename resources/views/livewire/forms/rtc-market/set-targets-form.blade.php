<div>
    <div class="bg-transparent shadow-none card">
        <div class="p-0 card-body">
            <form wire:submit.prevent="saveTargets" id="targetForm">
                <table
                    class="table mb-2 border table-bordered table-hover table-checkable table-highlight-head border-warning">
                    <thead class="border table-warning border-warning">
                        <tr>
                            <th colspan="3" class="text-center">
                                Before any submission within a given period, partners are expected to set their
                                targets. Please fill your details, and the form will be open for submission!
                            </th>
                        </tr>
                    </thead>
                    <thead class="border border-warning">
                        <tr>
                            <th colspan="3">

                                <p>Indicator: <span
                                        class="text-warning">{{ $targets->first()->Indicator->indicator_name }}</span>
                                </p>

                                <p>Project Year: <span
                                        class="text-warning text-capitalize">{{ $targets->first()->financialYear->number }}</span>
                                </p>
                                <p>Organisation/Partner: <span class="text-warning">{{ $organisation->name }}</span></p>

                            </th>
                        </tr>
                    </thead>
                    <thead class="border table-warning border-warning">
                        <tr class="text-uppercase" style="font-size: 12px;">
                            <th scope="col" class="text-secondary">Target Name</th>
                            <th scope="col" class="text-secondary">Target Value</th>
                            <th scope="col" class="text-secondary">Your Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($targets as $index => $target)
                            <tr>
                                <td>{{ $target['target_name'] }}</td>
                                <td>{{ $target['target_value'] }}</td>
                                <td>
                                    <input type="number"
                                        class="form-control @error('targets.' . $index . '.value')
                                        is-invalid
                                    @enderror"
                                        placeholder="Enter your value" wire:model="targets.{{ $index }}.value">

                                    @error('targets.' . $index . '.value')
                                        <x-error> {{ $message }}</x-error>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr></tr>
                        <td colspan="3">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal">
                                    Submit
                                </button>
                            </div>
                        </td>
                        </tr>
                    </tfoot>
                </table>


            </form>

            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to submit these targets?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning" wire:click="saveTargets()"
                                data-bs-dismiss="modal">
                                Yes, Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
