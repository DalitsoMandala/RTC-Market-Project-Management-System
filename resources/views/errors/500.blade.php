@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', 'Sorry, something went wrong')
@section('message', __('The server encountered an internal error and was unable to complete your request. Either the
    server is overloaded or there is an error in the application.'))
@section('error-img')
    <div class="error-img">
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <lord-icon src="https://cdn.lordicon.com/lltgvngb.json" trigger="loop" colors="primary:#e83a30,secondary:#e86830"
            style="width:300px;height:300px">
        </lord-icon>
    </div>
@endsection
