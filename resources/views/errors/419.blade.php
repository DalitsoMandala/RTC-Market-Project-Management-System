@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', 'Sorry, Page Expired')
@section('message', __('The page has expired due to inactivity. Please try again.'))
@section('error-img')
<div class="error-img">
    <img src="{{asset('assets/images/419-img.png')}}" alt="" class="img-fluid mx-auto d-block">
</div>
@endsection