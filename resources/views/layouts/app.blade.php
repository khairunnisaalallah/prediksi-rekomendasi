<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Web Gizi Anak')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Home</span></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('/') }}"><i class="ti ti-layout-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('diagnosis-gizi') }}"><i class="ti ti-file-description"></i><span class="hide-menu">Diagnosis Gizi</span></a></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('data-balita') }}"><i class="ti ti-article"></i><span class="hide-menu">Data Balita</span></a></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('panduan') }}"><i class="ti ti-cards"></i><span class="hide-menu">Panduan</span></a></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('login') }}"><i class="ti ti-login"></i><span class="hide-menu">Login</span></a></li>
            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('register') }}"><i class="ti ti-user-plus"></i><span class="hide-menu">Register</span></a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Main -->
    <div class="body-wrapper">
      <!-- Header -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="#" data-bs-toggle="dropdown">
                  <img src="{{ asset('assets/images/profile/user-1.jpg') }}" width="35" height="35" class="rounded-circle" alt="">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up">
                  <div class="message-body">
                    <a href="#" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i><p class="mb-0 fs-3">Profil</p>
                    </a>
                    <a href="{{ url('login') }}" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      @yield('content')

      <!-- Scripts -->
      <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
      <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
      <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
      <script src="{{ asset('assets/js/app.min.js') }}"></script>
      <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
      <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>

      @stack('scripts')
    </div>
  </div>
</body>
</html>