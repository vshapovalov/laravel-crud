<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @yield('html_attributes')>
<head >
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @yield('head')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ url('vendor/vshapovalov/crud/assets/css/admin.css?140') }}" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

    @yield('styles')
</head>
<body>
{{ csrf_field() }}
@yield('content')

@section('scripts')
    <script>
        window.App = {
            baseUrl: "{{ url('/') }}",
            crudPrefix: "{{ config('cruds.crud_prefix') }}",
            mediaPrefix: "{{ config('cruds.media_prefix') }}",
            mediaPath: "{{ session()->get('media.path', 'uploads') }}",
            uploadPath: "{{ '/storage' }}",
            locale: "{{ app()->getLocale() }}",
            user: {!! json_encode( auth()->user() ) !!},
            theme: {
                name: "{{ config('cruds.theme.name') }}",
                colors: {!! json_encode( config('cruds.theme.colors') ) !!}
            },
            l18n: {!! json_encode( __('cruds') ) !!},
            tinymce: {!! json_encode( config('cruds.tinymce') ) !!}
        };


    </script>

    <script src="{{ url('vendor/vshapovalov/crud/assets/js/admin.js?140') }}"></script>
@show
</body>
</html>