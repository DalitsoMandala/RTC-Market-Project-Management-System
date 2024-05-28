   <div class="row">
       <div class="col">
           @if (session()->has('error'))
               <x-error-alert>{{ session()->get('error') }}</x-error-alert>
           @endif
           @if (session()->has('success'))
               <x-success-alert>{{ session()->get('success') }}</x-success-alert>
           @endif
       </div>
   </div>
