@extends('layouts.dashboard')
@section('content')
    <h1>Hello Blog</h1>

    <p>
        This view is loaded from module: {!! config('blog.name') !!}
    </p>
@endsection