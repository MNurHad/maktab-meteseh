@extends('layouts.cms')
@section('cms_title', $title)
@section('cms_content')
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sectors.index') }}">Sektor</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $title }} Edit</h5>
                    <form class="row g-3 needs-validation" id="formSector" action="{{ route('admin.sectors.update', [$sektor->id]) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="col-md-12">
                            <label for="validationCustom01" class="form-label">Sektor Name</label>
                            <input type="text" class="form-control" id="validationCustom01" value="{{ $sektor->sektor }}" name="sektor" required>
                            <div class="invalid-feedback">Sektor is required</div>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('admin.sectors.index') }}" class="btn btn-danger">
                                <i class="bi bi-skip-backward"></i> Back
                            </a>
                            <button class="btn btn-linear" id="btnSubmit" type="submit">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSpinner"></span>
                                <span id="submitText"><i class="bi bi-save2-fill"></i> Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('cms_js')
<script>
$(document).ready(function () {
    $('#formSector').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let submitBtn = $('#btnSubmit');
        let spinner = $('#submitSpinner');
        let submitText = $('#submitText');

        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return;
        }

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        submitText.text('Processing...');

        // Kirim data pakai AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message || 'Data berhasil disimpan.',
                    confirmButtonColor: 'rgba(16, 214, 29, 1)'
                }).then(() => {
                    window.location.href = "{{ route('admin.sectors.index') }}";
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonColor: '#d33'
                });
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                submitText.html('<i class="bi bi-save2-fill"></i> Save');
            }
        });
    });
});
</script>
@endpush