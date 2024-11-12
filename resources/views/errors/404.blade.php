@extends('errors.minimal')
@section('title', __('Not Found'))
@section('code', 'Sorry, Page Not found')
@section('message', __('The page you are looking for was not found.'))
@section('error-img')
<div class="error-img">
    <img src="{{asset('assets/images/404-img.png')}}" alt="" class="img-fluid mx-auto d-block">
</div>
@endsection