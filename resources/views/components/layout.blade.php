
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Upgrade HTTP requests to HTTPS -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles

    <title>{{ config('app.name') }}{{ isset($title) ? ' - ' . $title : '' }}</title>

</head>


<body>

<!-- Placeholder for Response Message -->
<div id="responseMessage" class="mt-3 text-success"></div>

    <!-- Page Header -->
    <header>
        <div class="container-fluid pageheader">
            <div class="row">
                <div class="col-sm-2 companylogo">
                    <a href="{{ route('home') }}">
                        <img src="{{asset('/images/ww_primary lockup_white-01.png')}}" alt="DCWW logo">
                    </a>
                </div>
                <div class="col-auto apptitle">
                    {{ config('app.name') }}
                </div>
                <!-- Login / User details at top of screen -->
                <div class="col-sm-2 login">
                    <div class="dropdown pb-4">
                        @auth
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{asset('/images/user.png')}}" alt="profileimage" width="40" height="40" class="rounded-circle">
                            <span class="d-none d-sm-inline mx-1">{{ Auth::User()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            @if(Auth::User()->is_admin)
                                <li><a class="dropdown-item" href="{{ route('admin') }}">Admin</a></li>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Sign out</a></li>
                            </form>
                        </ul>
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- Main container -->
    <main class="container-fluid">
        <div class="row flex">
            <!--
                Navigation sidebar
                Only visible if the user is logged in
             -->
            @auth
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white">
                        <span class="fs-5 d-none d-sm-inline">Menu</span>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li class="nav-item">
                            <a href="/" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-house"></i> <span class="ms-1 d-none d-sm-inline">Home</span>
                            </a>
                        </li>
                        <li>

                            <ul class="nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                <li class="w-100">
                                    <nav class="navbar navbar-light bg-light navsearch">
                                        <form class="form-inline" method="GET" action="{{ route('search') }}">
                                            <div class="input-group">
                                                <input class="form-control border-end-0 border" type="search" name="search" placeholder="Search" value="{{ session('searchQuery', '') }}" aria-label="Search">
                                                <button class="btn btn-outline-secondary bg-white border-start-0 border-bottom-0 border ms-n5" type="submit">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </nav>
                                </li>


                                <!--
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Create new record</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Update record</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Actively remove</span></a>
                                </li>
                                -->
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-upload"></i> <span class="ms-1 d-none d-sm-inline">Bulk Update</span></a>
                        </li>
                        @if(Auth::User()->is_admin)
                        <li>
                            <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                                <i class="fs-4 bi-tools"></i> <span class="ms-1 d-none d-sm-inline">Admin</span></a>
                            <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Item</span> 1</a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Item</span> 2</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <li>
                            <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-envelope-at"></i> <span class="ms-1 d-none d-sm-inline">Report an Issue</span> </a>
                            <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 1</a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 2</a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 3</a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 4</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Customers</span> </a>
                        </li>
                    </ul>
                    <hr>
                </div>
                    @endauth
            </div>

            <div class="col-content
                @auth with-sidebar @endauth
                @guest no-sidebar @endguest">
                <div id="flash-container">
                    @include('flashmessage')
                    @yield('content')
                </div>

                {{ $slot }}
            </div>



            <!-- DataTables CSS -->
            <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">

            <!-- jQuery (required by DataTables) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <!-- DataTables JS -->
            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>

            @stack('scripts')

            @livewireScripts

            <!-- timeout for flash messages -->
            <script>
                $("document").ready(function() {
                    setTimeout(function () {
                        $("div.alert").remove();
                    }, 3000); // 3 secs
                });
            </script>


        </div>
    </main>

</body>
