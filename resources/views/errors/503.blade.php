@extends('errors::minimal')

@section('title', __('Site Under Maintenance'))
@section('code', 'Site Under Maintenance')
@section('message', __('The site is currently undergoing maintenance. Please check back later.'))
@section('error-img')
    <div class="error-img">

        <i class="p-5 border rounded-circle border-warning bx bx-wrench bx-tada text-warning bg-soft-warning"
            style="font-size:100px"></i>
        <h1 class="display-1 fw-bold">503</h1>
    </div>


@endsection


