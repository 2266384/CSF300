
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
    @livewireChartsScripts

    <title>{{ config('app.name') }}{{ isset($title) ? ' - ' . $title : '' }}</title>

    <script>
        // Get today's date in the format YYYY-MM-DD
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const day = String(today.getDate()).padStart(2, '0');

        const minDate = `${year}-${month}-${day}`;
    </script>

</head>


<body>

<!-- Placeholder for Response Message -->
<!-- <div id="responseMessage" class="mt-3 text-success"></div> -->

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
                        <div class="accordion" id="menuAccordion">
                            <!-- Home -->
                            <div class="accordion-item">
                                <p class="accordion-header" id="headingHome">
                                    <a href="/" class="nav-link align-middle px-0">
                                        <i class="fs-4 bi-house"></i>
                                        <span class="ms-1 d-none d-sm-inline">Home</span>
                                    </a>
                                </p>
                            </div>

                            <!-- Search -->
                            <div class="accordion-item">
                                        <nav class="navbar navbar-light bg-light navsearch">
                                            <form class="form-inline" method="GET" action="{{ route('search') }}">
                                                <div class="input-group">
                                                    <input class="form-control border-end-0 border" type="search" name="search" placeholder="Search"
                                                           value="{{ session('searchQuery', '') }}" aria-label="Search">
                                                    <button class="btn btn-outline-secondary bg-white border-start-0 border-bottom-0 border ms-n5" type="submit">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </nav>
                            </div>

                            <!-- Bulk Update -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingBulkUpdate">
                                    <a href="{{ route('bulk') }}" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi-upload"></i>
                                        <span class="ms-1 d-none d-sm-inline">Bulk Update - TODO</span>
                                    </a>
                                </h2>
                            </div>

                            <!-- Admin -->
                            @if(Auth::User()->is_admin)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingAdmin">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                                            <i class="fs-4 bi-tools"></i>
                                            <span class="ms-1 d-none d-sm-inline">Admin</span>
                                        </button>
                                    </h2>
                                    <div id="collapseAdmin" class="accordion-collapse open" aria-labelledby="headingAdmin">
                                        <div class="accordion-body">
                                            <ul class="nav flex-column">
                                                <li><a href="{{ route('users.index') }}" class="nav-link px-0 {{ isActiveRoute('users*') }}">Users <i class="bi bi-chevron-right"></i></a></li>
                                                <hr class="solid">
                                                <li><a href="{{ route('needcodes.index') }}" class="nav-link px-0 {{ isActiveRoute('needcodes*') }}">Needs <i class="bi bi-chevron-right"></i></a></li>
                                                <li><a href="{{ route('servicecodes.index') }}" class="nav-link px-0 {{ isActiveRoute('servicecodes*') }}">Services <i class="bi bi-chevron-right"></i></a></li>
                                                <hr class="solid">
                                                <li><a href="{{ route('organisations.index') }}" class="nav-link px-0 {{ isActiveRoute('organisations*') }}">Organisations <i class="bi bi-chevron-right"></i></a></li>
                                                <li><a href="{{ route('representatives.index') }}" class="nav-link px-0 {{ isActiveRoute('representatives*') }}">Representatives <i class="bi bi-chevron-right"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Metrics -->
                                <div class="accordion-item">
                                    <p class="accordion-header" id="viewMetrics">
                                        <a href="{{ route('metrics.index') }}" class="nav-link align-middle px-0">
                                            <i class="fs-4 bi-bar-chart"></i>
                                            <span class="ms-1 d-none d-sm-inline">Metrics - TODO</span>
                                        </a>
                                    </p>
                                </div>
                            @endif

                            <!-- Report an issue -->
                            <div class="accordion-item">
                                <p class="accordion-header" id="headingReportIssue">
                                    <a href="{{ route('report') }}" class="nav-link align-middle px-0">
                                        <i class="fs-4 bi-envelope-at"></i>
                                        <span class="ms-1 d-none d-sm-inline">Report an Issue</span>
                                    </a>
                                </p>
                            </div>

                            <!-- Sample Dropdown -->
                        <!--
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="sampleHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseReportIssue" aria-expanded="false" aria-controls="collapseReportIssue">
                                        <i class="fs-4 bi-envelope-at"></i>
                                        <span class="ms-1 d-none d-sm-inline">Sample Heading</span>
                                    </button>
                                </h2>
                                <div id="collapseReportIssue" class="accordion-collapse collapse" aria-labelledby="headingReportIssue">
                                    <div class="accordion-body">
                                        <ul class="nav flex-column">
                                            <li><a href="#" class="nav-link px-0"><span class="d-none d-sm-inline">Product</span> 1</a></li>
                                            <li><a href="#" class="nav-link px-0"><span class="d-none d-sm-inline">Product</span> 2</a></li>
                                            <li><a href="#" class="nav-link px-0"><span class="d-none d-sm-inline">Product</span> 3</a></li>
                                            <li><a href="#" class="nav-link px-0"><span class="d-none d-sm-inline">Product</span> 4</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        -->
                            <!-- Customers -->
                        <!--
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCustomers">
                                    <a href="#" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi-people"></i>
                                        <span class="ms-1 d-none d-sm-inline">Customers</span>
                                    </a>
                                </h2>
                            </div>
                        -->
                        </div>
                        <hr>
                    </div>
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
