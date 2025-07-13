@extends('errors.minimal')
@section('title', __('Not Found'))

@section('code', 'Sorry, Page Not found')
@section('message', __('The page you are looking for was not found.'))
@section('error-img')
    <div class="error-img">
        {{-- <img src="{{ asset('assets/images/404-img.png') }}" alt="" class="mx-auto img-fluid d-block"> --}}

        <i class="p-5 border rounded-circle border-warning bx bx-search-alt bx-tada text-warning bg-soft-warning"
            style="font-size:100px"></i>
        <h1 class="display-1 fw-bold">404</h1>
    </div>
@endsection
