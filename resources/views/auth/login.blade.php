<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - {{ config('app.name', 'NGD') }}</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('admin_panel/dist/assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('admin_panel/dist/assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <h2 style="color: purple; font-weight: bold;">NGD</h2>
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                            <form class="pt-3" method="POST" action="{{ route('login.submit') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        id="exampleInputEmail1" placeholder="Email" required autofocus
                                        value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        id="exampleInputPassword1" placeholder="Password" required>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit"
                                        class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN
                                        IN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('admin_panel/dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('admin_panel/dist/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin_panel/dist/assets/js/misc.js') }}"></script>
    <script src="{{ asset('admin_panel/dist/assets/js/settings.js') }}"></script>
    <script src="{{ asset('admin_panel/dist/assets/js/todolist.js') }}"></script>
    <script src="{{ asset('admin_panel/dist/assets/js/jquery.cookie.js') }}"></script>
</body>

</html>