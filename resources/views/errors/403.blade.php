@extends('errors::minimal')

@section('title', __(key: 'Forbidden'))
@section('code', 'This page is forbidden')
@section('message', __($exception->getMessage() ?: 'You are not allowed to access this page.'))
@section('error-img')
    <div class="error-img">
        <i class="bx bx-error-alt text-danger" style="font-size:190px"></i>
        <h1 class="display-1 fw-bold">403</h1>
    </div>
@endsection
