<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'RBJ Laboratory Information System')</title>

  <!-- Modern Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/summernote-lite.min.css') }}">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="{{ asset('css/modern-lis.css') }}">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/adminlte.min.js') }}"></script>
  <script src="{{ asset('js/toastr.min.js') }}"></script>
  <script src="{{ asset('js/summernote-lite.min.js') }}"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>
    var baseURL = "{{ url('/') }}/";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  </script>

  <style>
    .loader-full {
      background-color: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(4px);
      width: 100%;
      height: 100%;
      z-index: 999999;
      position: fixed;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      display: none;
    }
    .user-status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #10B981;
      display: inline-block;
      margin-right: 6px;
      box-shadow: 0 0 8px #10B981;
    }
  </style>
  @stack('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="loader-full">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Processing...</span>
    </div>
  </div>

  <div class="wrapper">
    <!-- Modern Top Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars-staggered"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block ml-2">
          <span class="badge badge-light border text-muted px-3 py-2 font-weight-normal">
            <i class="far fa-calendar-alt text-primary mr-1"></i> {{ date('l, d F Y') }}
          </span>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto align-items-center">
        <!-- Lab / Org Indicator -->
        <li class="nav-item d-none d-md-inline-block mr-3">
          <span class="badge badge-primary px-3 py-2">
            <span class="user-status-dot"></span>
            {{ auth()->user()->lab?->name ?? 'RBJ Diagnostics' }}
          </span>
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" style="gap: 8px;">
            <div class="rounded-circle bg-primary text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 13px;">
              {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <span class="d-none d-md-inline-block font-weight-bold" style="font-size: 0.9rem;">
              {{ auth()->user()->name ?? 'User' }}
            </span>
            <i class="fas fa-chevron-down text-muted" style="font-size: 10px;"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0" style="border-radius: 12px;">
            <div class="dropdown-header text-left bg-light rounded-top p-3">
              <h6 class="font-weight-bold text-dark mb-0">{{ auth()->user()->name ?? 'Staff User' }}</h6>
              <small class="text-muted">{{ auth()->user()->email ?? '' }}</small><br>
              <span class="badge badge-info mt-1">{{ auth()->user()->role?->name ?? 'User' }}</span>
            </div>
            <div class="dropdown-divider m-0"></div>
            <a href="{{ route('profile.index') }}" class="dropdown-item py-2">
              <i class="fas fa-user-circle mr-2 text-primary"></i> My Profile & Signature
            </a>
            <a href="{{ route('password.change') }}" class="dropdown-item py-2">
              <i class="fas fa-key mr-2 text-warning"></i> Change Password
            </a>
            <div class="dropdown-divider m-0"></div>
            <form action="{{ route('logout') }}" method="POST" class="p-2">
              @csrf
              <button type="submit" class="btn btn-danger btn-block btn-sm">
                <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
              </button>
            </form>
          </div>
        </li>
      </ul>
    </nav>

    <!-- Modern Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="{{ url('/') }}" class="brand-link d-flex align-items-center">
        <i class="fas fa-dna text-primary ml-2 mr-2" style="font-size: 22px;"></i>
        <span class="brand-text">RBJLIS <span style="font-size: 10px; font-weight: 500; opacity: 0.7; letter-spacing: 1px;" class="badge badge-light ml-1">LIMS</span></span>
      </a>

      @php
        $sidebarType = session('sidebar', $sidebar ?? 'frontoffice');
        if (auth()->check()) {
            if (auth()->user()->role_id == 1) $sidebarType = 'superadmin';
            elseif (auth()->user()->role_id == 2) $sidebarType = 'labadmin';
            elseif (auth()->user()->role_id == 8) $sidebarType = 'll';
            else $sidebarType = 'frontoffice';
        }
      @endphp

      @if($sidebarType == 'superadmin')
        @include('partials.sidebar-superadmin')
      @elseif($sidebarType == 'labadmin')
        @include('partials.sidebar-labadmin')
      @elseif($sidebarType == 'll')
        @include('partials.sidebar-ll')
      @else
        @include('partials.sidebar-frontoffice')
      @endif
    </aside>

    <!-- Content Area -->
    <div class="content-wrapper">
      <!-- Flash Alert Banners -->
      <div class="container-fluid px-3">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px; background: #ECFDF5; color: #065F46;">
            <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('success') }}
            <button type="button" class="close text-success" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px; background: #FEF2F2; color: #991B1B;">
            <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> {{ session('error') }}
            <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <ul class="mb-0 pl-3">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </div>

    <!-- Modern Footer -->
    <footer class="main-footer bg-white border-top text-muted text-sm py-3 px-4 d-flex justify-content-between align-items-center">
      <div>
        <strong>RBJLIS &copy; {{ date('Y') }}</strong> - Modern Healthcare Laboratory Information System
      </div>
      <div class="d-none d-sm-inline-block">
        <span class="badge badge-light border">v2.5.0 SaaS Edition</span>
      </div>
    </footer>
  </div>

  @stack('scripts')
</body>
</html>
