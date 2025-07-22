@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', 'Sorry, Page Expired')
@section('message', __('Your session has expired. Please refresh or log in again.'))
@section('error-img')
    <div class="error-img">
        <i class="p-5 border rounded-circle border-warning bx bx-hourglass bx-tada text-warning bg-soft-warning"
            style="font-size:100px"></i>
        <h1 class="display-1 fw-bold">419</h1>
    </div>
@endsection
