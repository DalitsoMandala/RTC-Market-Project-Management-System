@extends('errors::minimal')

@section('title', __(key: 'Forbidden'))
@section('code', 'This page is forbidden')
@section('message', __($exception->getMessage() ?: 'You are not allowed to access this page.'))
@section('error-img')
<div class="error-img">
    <img src="{{asset('assets/images/403-img.png')}}" alt="" class="img-fluid mx-auto d-block">
</div>
@endsection