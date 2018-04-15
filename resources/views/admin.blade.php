<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @yield('html_attributes')>
<head >
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
@yield('head')

<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ url('vendor/vshapovalov/crud/assets/css/admin.css?128') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
@yield('content')

@section('scripts')
    <script>
        window.baseUrl = "{{ url('/') }}";
        window.crudPrefix = "{{ config('cruds.crud_prefix') }}";
        window.mediaPrefix = "{{ config('cruds.media_prefix') }}";
        window.mediaPath = "{{ session()->get('media.path', 'uploads') }}";
        window.uploadPath = "{{ '/storage' }}";
    </script>

    <script src="{{ url('vendor/vshapovalov/crud/assets/js/admin.js?128') }}"></script>
@show
</body>
</html>