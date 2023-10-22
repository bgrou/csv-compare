<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'CSV Compare')</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        @yield('styles')
        <style>
            body {
                padding: 20px;
                font-size: 14px;
            }

            .new {
                background-color: #ccffd9;
            }

            .alter {
                background-color: #a7c7e7;
            }

            table {
                margin-top: 20px;
            }
        </style>
    </head>

    <body class="bg-light ">

        @if ($errors->any())
            <div id="snackbar" class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
        @yield('scripts')
    </body>
</html>
