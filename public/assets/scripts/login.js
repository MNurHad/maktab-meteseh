$(document).ready(function () {
    $('#toggle-password').on('click', function () {
        const $input = $('#yourPassword');
        const $icon = $(this).find('i');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    $('#formLogin').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#login-btn');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.btn-text');

        const emailVal = $('#yourUsername').val().trim();
        const passwordVal = $('#yourPassword').val().trim();

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('Processing Login...');

        const loginUrl = $('#formLogin').attr('action');

        $.ajax({
            url: loginUrl,
            method: "POST",
            data: {
                email: emailVal,
                password: passwordVal,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
                if (resp.redirect) {
                    window.location.href = resp.redirect;
                }
            },
            error: function (xhr) {
                let errorMessage = "Login failed. Please check your credentials.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: errorMessage
                });

                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('Log in');
            }
        });
    });
});