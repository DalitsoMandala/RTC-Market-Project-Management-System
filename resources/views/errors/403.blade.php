@extends('errors::minimal')

@section('title', __(key: 'Forbidden'))
@section('code', 'This page is forbidden')
@section('message', __($exception->getMessage() ?: 'You are not allowed to access this page.'))
@section('error-img')
<div class="error-img">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <lord-icon src="https://cdn.lordicon.com/lltgvngb.json" trigger="loop" colors="primary:#e83a30,secondary:#e86830"
        style="width:300px;height:300px">
    </lord-icon>
</div>
@endsection
