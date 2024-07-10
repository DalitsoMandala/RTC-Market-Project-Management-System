   <div class="row">
       <div class="col">
           @if (session()->has('error'))
               <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
           @endif
           @if (session()->has('success'))
               <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
           @endif

           @if (session()->has('validation_error'))
               <x-error-alert>{!! session()->get('validation_error') !!}</x-error-alert>
           @endif

           @if (session()->has('import_failures'))
               <div class="alert alert-danger " x-data="{
                   is_open: false
               }" role="alert">
                   <div class="d-flex justify-content-between align-items-center">
                       There were errors on your uploaded file! <a @click="is_open = !is_open" href="javascript: void(0);"
                           class="btn btn-danger btn-sm">View errors <i class="bx bx-caret-down"></i></a>
                   </div>
                   <div x-show="is_open">
                       <hr>

                       <ul>
                           @foreach (session()->get('import_failures') as $failure)
                               <li class="error-item">

                                   <strong>Row {{ $failure['row'] }}:</strong>
                                   @foreach ($failure['errors'] as $error)
                                       <div>{{ $error }}</div>
                                   @endforeach
                               </li>
                           @endforeach
                       </ul>
                   </div>
               </div>
           @endif


       </div>
   </div>
