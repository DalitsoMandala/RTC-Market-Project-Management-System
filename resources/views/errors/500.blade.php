@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', 'Sorry, something went wrong')
@section('message', __('The server encountered an internal error and was unable to complete your request. Either the
    server is overloaded or there is an error in the application.'))
@section('error-img')
    <div class="error-img">
        <i class="bx bx-error-alt text-danger" style="font-size:190px"></i>
        <h1 class="display-1 fw-bold">500</h1>
    </div>
@endsection
