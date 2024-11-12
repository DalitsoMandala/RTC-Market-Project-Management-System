@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', 'Sorry, something went wrong')
@section('message', __('The server encountered an internal error and was unable to complete your request. Either the server is overloaded or there is an error in the application.'))
@section('error-img')
<div class="error-img">
    <img src="{{asset('assets/images/500-img.png')}}" alt="" class="img-fluid mx-auto d-block">
</div>
@endsection