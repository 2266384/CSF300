
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}{{ isset($title) ? ' - ' . $title : '' }}</title>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style type="text/css">
        i{
            font-size: 50px;
        }
    </style>

</head>

<div>

    <main>



        {{ $slot }}

        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
        <!-- <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script> -->
        <!-- <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script> -->
        @stack('scripts')

        <!-- timeout for flash messages -->
        <script>
            $("document").ready(function() {
                setTimeout(function () {
                    $("div.alert").remove();
                }, 3000); // 3 secs
            });
        </script>
    </main>

</div>
