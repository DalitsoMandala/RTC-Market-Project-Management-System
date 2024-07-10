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

       </div>
   </div>
