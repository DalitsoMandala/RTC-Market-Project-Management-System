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
           @endif


       </div>
   </div>
