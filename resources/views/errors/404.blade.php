@extends('errors.minimal')
@section('title', __('Not Found'))

@section('code', 'Sorry, Page Not found')
@section('message', __('The page you are looking for was not found.'))
@section('error-img')
    <div class="error-img">
        {{-- <img src="{{ asset('assets/images/404-img.png') }}" alt="" class="mx-auto img-fluid d-block"> --}}
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <lord-icon src="https://cdn.lordicon.com/lltgvngb.json" trigger="loop" colors="primary:#e83a30,secondary:#e86830"
            style="width:300px;height:300px">
        </lord-icon>

    </div>
@endsection
