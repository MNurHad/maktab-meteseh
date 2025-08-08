@extends('layouts.cms')
@section('cms_title', $title)
@section('cms_content')
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sectors.index') }}">Sektor</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $title }} Create</h5>
                    <form id="formSector" class="row g-3 needs-validation" action="{{ route('admin.coordinators.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="col-md-4">
                            <label for="sector_id" class="form-label">Sektor</label>
                            <select name="sector_id" id="sector_id" class="select2" style="width: 100%;">
                                <option value="">-- Pilih Sektor --</option>
                                @foreach(listSector() as $sector)
                                    <option value="{{ $sector->id }}">{{ $sector->sektor }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sektor is required</div>
                        </div>

                        <div class="col-md-4">
                            <label for="cp_name" class="form-label">CP Name</label>
                            <input type="text" class="form-control" name="cp_name" id="cp_name" required>
                            <div class="invalid-feedback">CP Name is required</div>
                        </div>

                        <div class="col-md-4">
                            <label for="phone" class="form-label">CP Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone" required>
                            <div class="invalid-feedback">CP Phone is required</div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-linear" id="btnSubmit" type="submit">
                                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="submitText"><i class="bi bi-save2-fill"></i> Save</span>
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