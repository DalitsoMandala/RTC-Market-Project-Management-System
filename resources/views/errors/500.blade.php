@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', 'Sorry, something went wrong')
@section('message',
    __('The server encountered an internal error and was unable to complete your request. Either the
    server is overloaded or there is an error in the application.'))
@section('error-img')
    <div class="error-img">
        <i class="p-5 border rounded-circle border-warning bx bx-cog bx-spin text-warning bg-soft-warning"
            style="font-size:100px"></i>
        <h1 class="display-1 fw-bold">500</h1>
    </div>
@endsection
