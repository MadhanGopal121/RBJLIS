<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sign In - RBJLIS')</title>

  <!-- Modern Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modern-lis.css') }}">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/toastr.min.js') }}"></script>

  <style>
    body.auth-page {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      min-height: 100vh;
      background: radial-gradient(circle at 10% 20%, rgb(15, 23, 42) 0%, rgb(30, 41, 59) 90.2%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
      position: relative;
      overflow-x: hidden;
    }

    .auth-bg-circle {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      pointer-events: none;
      z-index: 0;
    }
    .auth-bg-circle-1 {
      width: 400px;
      height: 400px;
      background: rgba(79, 70, 229, 0.25);
      top: -100px;
      right: -100px;
    }
    .auth-bg-circle-2 {
      width: 350px;
      height: 350px;
      background: rgba(14, 165, 233, 0.2);
      bottom: -80px;
      left: -80px;
    }

    .auth-card {
      width: 100%;
      max-width: 460px;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 2.5rem;
      position: relative;
      z-index: 1;
    }
  </style>
  @stack('css')
</head>
<body class="auth-page">
  <div class="auth-bg-circle auth-bg-circle-1"></div>
  <div class="auth-bg-circle auth-bg-circle-2"></div>

  <div class="auth-card">
    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
