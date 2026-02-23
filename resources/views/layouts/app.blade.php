<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ config('app.name', 'Admin Panel') }}</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('admin_panel/dist/assets/images/favicon.png') }}" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @yield('styles')
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    /* Custom UI Styles matching screenshot */
    body {
      background-color: #f4f5fa;
      font-family: 'Inter', sans-serif;
    }

    .main-panel {
      background: #fdfdfd;
    }

    .page-header-custom {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 2rem;
      padding-bottom: 0px;
    }

    .page-title-custom {
      color: #b66df8;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      letter-spacing: -0.5px;
    }

    .page-title-icon-custom {
      margin-right: 10px;
      font-size: 1.5rem;
    }

    .page-subtitle-custom {
      color: #8e94a9;
      font-size: 0.85rem;
      margin-left: 2px;
    }

    .card-custom {
      border: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
      border-radius: 8px;
      background: #fff;
    }

    .btn-purple-custom {
      background: #b66df8;
      color: white !important;
      border: none;
      font-weight: 600;
      padding: 0.6rem 1.2rem;
      border-radius: 4px;
      font-size: 0.85rem;
      text-decoration: none !important;
      display: inline-block;
    }

    .btn-purple-custom:hover {
      background: #a55eea;
    }

    .btn-gradient-custom {
      background: linear-gradient(135deg, #c571fb, #a174ff);
      color: white !important;
      border: none;
      font-weight: 600;
      padding: 0.6rem 1.5rem;
      border-radius: 4px;
      font-size: 0.9rem;
      text-decoration: none !important;
      display: inline-block;
    }

    .btn-gradient-custom:hover {
      opacity: 0.9;
    }

    .btn-edit-custom {
      background-color: #fcd436;
      color: white !important;
      border: none;
      padding: 0.35rem 0.8rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.8rem;
      text-decoration: none !important;
      display: inline-block;
    }

    .btn-edit-custom:hover {
      background-color: #e5c332;
    }

    .btn-delete-custom {
      background-color: #ff7b94;
      color: white !important;
      border: none;
      padding: 0.35rem 0.8rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.8rem;
      text-decoration: none !important;
      display: inline-block;
    }

    .btn-delete-custom:hover {
      background-color: #e66f85;
    }

    .btn-cancel-custom {
      background-color: #f8f9fa;
      color: #333 !important;
      border: none;
      padding: 0.6rem 1.5rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.9rem;
      text-decoration: none !important;
      display: inline-block;
    }

    .btn-cancel-custom:hover {
      background-color: #e2e6ea;
    }

    .btn-back-custom {
      background-color: #eceef2;
      color: #b66df8 !important;
      border: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      text-decoration: none !important;
    }

    .btn-back-custom i {
      margin-right: 5px;
      font-size: 1rem;
    }

    .btn-back-custom:hover {
      background-color: #dcdce6;
      color: #a55eea !important;
    }

    .stats-badge {
      background-color: #ffffff;
      color: #333;
      padding: 0 1.2rem;
      border-radius: 4px;
      font-weight: 700;
      display: flex;
      align-items: center;
      font-size: 0.85rem;
      height: 38px;
    }

    .stats-badge i {
      margin-right: 8px;
      color: #555;
      margin-top: 2px;
    }

    .badge-active {
      background-color: #2bd7ab;
      color: white;
      padding: 0.4rem 0.65rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.75rem;
    }

    .badge-inactive {
      background-color: #ff7b94;
      color: white;
      padding: 0.4rem 0.65rem;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.75rem;
    }

    .table-custom {
      width: 100%;
      margin-bottom: 1rem;
      color: #333;
    }

    .table-custom thead th {
      border-top: none;
      border-bottom: 1px solid #f0f0f0;
      font-weight: 700;
      color: #111;
      padding: 1.2rem 1rem;
      font-size: 0.85rem;
    }

    .table-custom tbody tr {
      background-color: #ffffff;
    }

    .table-custom tbody td {
      border-top: 1px solid #f0f0f0;
      padding: 1.2rem 1rem;
      vertical-align: middle;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .custom-search-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding: 0.5rem 0;
    }

    .form-label-custom {
      color: #333;
      font-weight: 500;
      margin-bottom: 0.5rem;
      font-size: 0.85rem;
    }

    .form-control-custom {
      border: 1px solid #e1e1e1;
      border-radius: 4px;
      box-shadow: none;
      padding: 0.8rem 1rem;
      font-size: 0.9rem;
      background: #fff;
      width: 100%;
    }

    .form-control-custom:focus {
      border-color: #b66df8;
      box-shadow: none;
      outline: none;
    }

    .file-upload-wrapper {
      display: flex;
      align-items: stretch;
      border: 1px solid #e1e1e1;
      border-radius: 4px;
      overflow: hidden;
    }

    .file-upload-wrapper input[type="file"] {
      display: none;
    }

    .file-upload-btn {
      background: #f8f9fa;
      border-right: 1px solid #e1e1e1;
      padding: 0.8rem 1.5rem;
      color: #000;
      font-weight: 700;
      font-size: 0.85rem;
      margin: 0;
      cursor: pointer;
    }

    .file-upload-text {
      padding: 0.8rem 1rem;
      color: #aaa;
      flex-grow: 1;
      font-size: 0.85rem;
      background: #fff;
    }

    .custom-checkbox-label {
      display: flex;
      align-items: center;
      cursor: pointer;
      font-size: 0.9rem;
      color: #333;
    }

    .custom-checkbox-label input {
      margin-right: 8px;
      width: 18px;
      height: 18px;
      accent-color: #b66df8;
    }

    .entries-select-box {
      border: 1px solid #e1e1e1;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      color: #6c757d;
      font-size: 0.85rem;
      background: #fff;
      outline: none;
      margin: 0 8px;
    }

    .search-input-box {
      border: 1px solid #e1e1e1;
      border-radius: 4px;
      padding: 0.45rem 0.8rem;
      font-size: 0.85rem;
      color: #6c757d;
      width: 250px;
    }

    .sidebar .nav .nav-item.active>.nav-link .menu-title {
      font-weight: 700;
      color: #a561f0 !important;
    }

    .sidebar .nav .nav-item.active>.nav-link i.menu-icon {
      color: #a561f0 !important;
    }

    .sidebar .nav .nav-item:not(.active)>.nav-link .menu-title {
      color: #3e4b5b !important;
      font-weight: 400;
    }

    .sidebar .nav .nav-item:not(.active)>.nav-link i.menu-icon {
      color: #bba8bff5 !important;
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="{{ url('/') }}"
          style="color: purple; font-weight: bold; font-size: 24px;">Admin Panel</a>
        <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}" style="color: purple; font-weight: bold;">AP</a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
              aria-expanded="false">
              <div class="nav-profile-img">
                <img src="{{ asset('admin_panel/dist/assets/images/faces/face1.jpg') }}" alt="image">
                <span class="availability-status online"></span>
              </div>
              <div class="nav-profile-text">
                <p class="mb-1 text-black">{{ Auth::user()->name ?? 'Admin' }}</p>
              </div>
            </a>
            <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout me-2 text-primary"></i> Signout
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
          data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
              <div class="nav-profile-image">
                <img src="{{ asset('admin_panel/dist/assets/images/faces/face1.jpg') }}" alt="profile" />
                <span class="login-status online"></span>
              </div>
              <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Admin' }}</span>
                <span class="text-secondary text-small">Administrator</span>
              </div>
            </a>
          </li>
          <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/admin/dashboard') }}">
              <span class="menu-title">Dashboard</span>
              <i class="mdi mdi-home menu-icon"></i>
            </a>
          </li>
          <li class="nav-item pt-3">
            <span class="nav-link text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem">Content
              Management</span>
          </li>
          <li class="nav-item {{ request()->is('admin/categories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('categories.index') }}">
              <span class="menu-title">Categories</span>
              <i class="mdi mdi-folder menu-icon"></i>
            </a>
          </li>
          <li class="nav-item {{ request()->is('admin/subcategories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('subcategories.index') }}">
              <span class="menu-title">Subcategories</span>
              <i class="mdi mdi-folder-outline menu-icon"></i>
            </a>
          </li>
          <li class="nav-item {{ request()->is('admin/child-categories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('child-categories.index') }}">
              <span class="menu-title">Child Categories</span>
              <i class="mdi mdi-file-tree menu-icon"></i>
            </a>
          </li>
          <li class="nav-item {{ request()->is('admin/items*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('items.index') }}">
              <span class="menu-title">Items</span>
              <i class="mdi mdi-view-list menu-icon"></i>
            </a>
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Admin Panel &copy;
              {{ date('Y') }}.</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ asset('admin_panel/dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('admin_panel/dist/assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('admin_panel/dist/assets/js/misc.js') }}"></script>

  @if(session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: "{{ session('success') }}",
          timer: 5000,
          timerProgressBar: true,
          showConfirmButton: false
        });
      });
    </script>
  @endif

  @if(session('error'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: "{{ session('error') }}",
          timer: 5000,
          timerProgressBar: true,
          showConfirmButton: false
        });
      });
    </script>
  @endif

  @yield('scripts')
</body>

</html>