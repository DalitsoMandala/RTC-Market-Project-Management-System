@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', 'Sorry, Page Expired')
@section('message', __('The page has expired due to inactivity. Please try again.'))
@section('error-img')
<div class="error-img">
    <i class="bx bx-error-alt text-danger" style="font-size:190px"></i>
    <h1 class="display-1 fw-bold">419</h1>
</div>
@endsection
