var FormValidation = function () {
    var formValidation;
    var request;
    var callbackBeforeSend;
    var callbackSuccess;
    var callbackError;
    var callbackNewOption;

    var onSubmit = function (node) {
        if (request) request.abort();

        var url = node[0].action;
        var btnSubmit = node.find('button[type=submit]')[0];
        var l = Ladda.create(btnSubmit);

        // Disable tombol & start loading
        btnSubmit.disabled = true;
        l.start();

        // Buat FormData dari form element
        var formData = new FormData(node[0]);

        // Kalau perlu callbackBeforeSend, bisa pakai untuk manipulasi formData
        if (callbackBeforeSend) {
            // callbackBeforeSend harus return FormData juga atau void (ubah formData di tempat)
            var newFormData = callbackBeforeSend(node, formData);
            if (newFormData instanceof FormData) formData = newFormData;
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,  // wajib
            processData: false,  // wajib
        }).done(function(data) {
            if (callbackSuccess) callbackSuccess(data);
            else location.reload();
        }).fail(function(xhr) {
            const message = xhr.responseJSON?.message || xhr.statusText;

            if ([422, 429].includes(xhr.status)) {
                if (callbackError) callbackError(xhr.responseJSON, node);
            } else {
                if (callbackError) callbackError(null, node);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: message,
                });
            }
        }).always(function() {
            btnSubmit.disabled = false;
            l.stop();
        });
    };

    return {
        init: function (formId, cbSuccess, cbError, cbBeforeSend, cbNewOption) {
            formValidation = $(formId);
            callbackSuccess = cbSuccess;
            callbackError = cbError;
            callbackBeforeSend = cbBeforeSend;
            callbackNewOption = cbNewOption;

            formValidation.validate({
                submitHandler: function (form, event) {
                    event.preventDefault();
                    onSubmit($(form));
                },
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                }
            });
        },
        handleClientError: function (node, errors) {
            $.each(errors, function (field, messages) {
                var input = node.find('[name="' + field + '"]');
                input.addClass('is-invalid');
                if (!input.next('.invalid-feedback').length) {
                    input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                } else {
                    input.next('.invalid-feedback').text(messages[0]);
                }
            });
        }
    }
}();
