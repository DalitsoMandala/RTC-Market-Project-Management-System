<div class="row">
    <div class="col">
        @if (session()->has('error'))
            <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
        @endif
        @if (session()->has('success'))
            <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
        @endif

        @if (session()->has('info'))
            <x-warning-alert> {!! session()->get('info') !!}</x-warning-alert>
        @endif


        @if (session()->has('notice'))
            <div class="alert alert-secondary alert-border-left" x-ref="warningAlert" x-data x-init="() => {
            
                let object = $($refs.warningAlert);
                object.fadeTo(30000, 0).slideUp(500);
            }">
                <strong>Notice!</strong>
                {!! session()->get('notice') !!}
            </div>
        @endif

        @if (session()->has('validation_error'))
            <x-error-alert>{!! session()->get('validation_error') !!}</x-error-alert>
        @endif

        @if (session()->has('import_failures'))
            <x-error-alert>
                <div x-data="{
                    is_open: false
                }">
                    <span class="d-flex justify-content-between align-items-center">
                        There were errors on your uploaded file! <a @click="is_open = !is_open"
                            href="javascript: void(0);" class="btn btn-theme-red btn-sm">View errors <i
                                class="bx bx-caret-down"></i></a>
                    </span>
                    <div x-show="is_open">
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered border-danger">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Errors</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach (session()->get('import_failures') as $failure)
                                        <tr>
                                            <td><strong>{{ $failure['row'] }}</strong></td>
                                            <td>
                                                <ul>
                                                    @foreach ($failure['errors'] as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </x-error-alert>
        @endif




    </div>
</div>
