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
  <link href="{{ asset('assets/components/core/img/favicon.ico') }}" rel="icon">

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
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/components/core/css/style.css') }}" rel="stylesheet">
  @stack('cms_css')
  <style>
    .initial__loading {
        position: fixed;
        z-index: 99999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: sans-serif;
        gap: 20px;
    }

    .spinner-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
    }

    .spinner {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: conic-gradient(#e49b0f, #f1ae2f);
        mask: radial-gradient(farthest-side, transparent 65%, black 66%);
        -webkit-mask: radial-gradient(farthest-side, transparent 65%, black 66%);
        animation: spin 1s linear infinite;
    }

    .spinner-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        object-fit: contain;
        z-index: 2;
    }

    .initial__loading span {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        letter-spacing: 0.5px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-linear {
        background: linear-gradient(135deg, #e49b0f, #f1ae2f);
        color: #fdfdfd !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px; /* sama dengan .form-control */
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px; /* atur agar teks vertikal tengah */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px; /* posisikan panah di tengah */
    }
  </style>
</head>
<body>
    
    <div class="initial__loading" id="loadingOverlay">
        <div class="spinner-wrapper">
            <div class="spinner"></div>
            <img src="{{ asset('assets/components/core/img/logo.png') }}" alt="Logo" class="spinner-logo">
        </div>
        <span>Loading...</span>
    </div>
    
  @includeWhen(empty($single), 'includes.header')

  @includeWhen(empty($single), 'includes.sidebar')

  <main id="main" class="main">
    @yield('cms_content')
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>{{ env('APP_NAME') }}</span></strong>. All Rights Reserved
    </div>
  </footer>

  <!-- Vendor JS Files -->
    <!-- jQuery must come first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/components/core/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/components/core/vendor/php-email-form/validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Template Main JS File -->
   <script src="{{ asset('assets/components/core/js/main.js') }}"></script>
   <script>
        $(document).on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            }, 100);
        });

        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        $(document).ready(function () {
            var request;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.initial__loading').delay(200).fadeOut(400);

            $('#btn-notification-mark').on('click', function (e) {
                e.preventDefault();

                if (request) request.abort();

                const self = $(this);
                self.prop('disabled', true);
                self.text('Loading...');

                request = $.post(self.data('url'));
                request.done(function () {
                    location.reload();
                });

                request.always(function () {
                    self.prop('disabled', false);
                    self.text('Mark all as read')
                });
            });
        });

        function toTitleCase(str) {
            return str.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
        }

        $(document).ready(function () {
            $('#logout-item').on('click', function (e) {
                e.preventDefault();

                const logoutUrl = $(this).attr('href');

                Swal.fire({
                    title: 'Logout?',
                    text: "Anda yakin ingin keluar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    customClass: {
                        confirmButton: 'swal-confirm-gradient',
                    },
                    preConfirm: () => {
                        return $.ajax({
                            url: logoutUrl,
                            type: 'POST'
                        }).then(function (response) {
                            window.location.href = '{{ route("admin.showLogin") }}';
                        }).catch(function (xhr) {
                            Swal.showValidationMessage('Logout gagal. Silakan coba lagi.');
                        });
                    }
                });
            });
        });
    </script>
    @if($errors->any())
        <script>
            swal.fire({
                title: 'Error!',
                html: `
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            {!! implode('', $errors->all('<li>:message</li>')) !!}
                        </ul>
                    </div>
                `,
                icon: 'error',
            });
        </script>

    @elseif(session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
            });
        </script>
    @elseif(session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
            });
        </script>
    @endif
    @stack('cms_js')
</body>
</html>