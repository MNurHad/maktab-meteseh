<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{ env('APP_NAME') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="{{ asset('assets/components/core/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/components/core/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/components/core/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/components/core/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/components/core/css/style.css') }}" rel="stylesheet">
  <style>
    .btn-linear {
        background: linear-gradient(135deg, #e49b0f, #f1ae2f);
        color: #fdfdfd !important;
    }
  </style>
</head>

<body>

  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block">{{ env('APP_NAME') }}</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login Portal</h5>
                  </div>

                  <form id="formLogin" action="{{ route('admin.loginProses') }}" method="POST" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="col-12">
                            <label for="yourUsername" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="yourUsername" required>
                            <div id="email-error-empty" class="invalid-feedback d-none">Email is required.</div>
                            <div id="email-error-invalid" class="invalid-feedback d-none">Invalid email format.</div>
                        </div>

                        <div class="col-12 position-relative">
                            <label for="yourPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="yourPassword" required>
                            </div>
                            <div id="password-error-empty" class="invalid-feedback d-none">Password is required.</div>
                            <div id="password-error-length" class="invalid-feedback d-none">
                                Password must be at least 6 characters and contain uppercase, lowercase, and number.
                            </div>
                        </div>

                        <div class="col-12">
                            <button id="login-btn" class="btn btn-linear d-block w-100 mt-3" type="submit">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span class="btn-text">Log in</span>
                            </button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="{{ asset('assets/components/core/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/components/core//vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/components/core/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('assets/components/core/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/components/core/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/components/core/js/main.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('assets/scripts/login.js') }}"></script>
</body>
</html>