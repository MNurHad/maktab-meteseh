@extends('layouts.cms')
@section('cms_title', $title)
@section('cms_content')
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.maktabs.index') }}">Maktabs</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $title }} Edit</h5>
                    <!-- Custom Styled Validation -->
                     <form id="formMaktab" class="row g-3 needs-validation" action="{{ route('admin.maktabs.update', [$maktab->id]) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Sektor -->
                        <div class="col-md-3">
                            <label for="sector_id" class="form-label">Sektor</label>
                            <select name="sector_id" id="sector_id" class="select2 form-control" required>
                                <option value="">-- Pilih Sektor --</option>
                                @foreach(listSector() as $sector)
                                    <option value="{{ $sector->id }}" {{ ($sector->id == $maktab->sector_id) ? 'selected' : '' }}>{{ $sector->sektor }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sektor wajib diisi</div>
                        </div>

                        <!-- Coordinator -->
                        <div class="col-md-3">
                            <label for="coordinator_id" class="form-label">Coordinator</label>
                            <select name="coordinator_id" id="coordinator_id" class="select2 form-control" required>
                                <option value="">-- Pilih Koordinator --</option>
                                @foreach(listCoordinator() as $coor)
                                    <option value="{{ $coor->id }}" {{ ($coor->id == $maktab->coordinator_id) ? 'selected' : '' }}>{{ $coor->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Coordinator wajib diisi</div>
                        </div>

                        <!-- CP Name -->
                        <div class="col-md-3">
                            <label for="cp_name" class="form-label">CP Name</label>
                            <input type="text" id="cp_name" class="form-control" disabled>
                        </div>

                        <!-- CP Phone -->
                        <div class="col-md-3">
                            <label for="cp_phone" class="form-label">CP Phone</label>
                            <input type="text" id="cp_phone" class="form-control" disabled>
                        </div>

                        <hr>

                        <!-- Host Name -->
                        <div class="col-md-3">
                            <label for="host_name" class="form-label">Host Name</label>
                            <input type="text" name="host_name" id="host_name" class="form-control" value="{{ $host['owner'] ?? '' }}" required>
                            <div class="invalid-feedback">Host Name wajib diisi</div>
                        </div>

                        <!-- Host Phone -->
                        <div class="col-md-3">
                            <label for="host_phone" class="form-label">Host Phone</label>
                            <input type="text" name="host_phone" id="phone" class="form-control" value="{{ $host['phone'] ?? '' }}" required>
                            <div class="invalid-feedback">Host Phone wajib diisi</div>
                        </div>

                        <!-- Capacity -->
                        <div class="col-md-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" name="capacity" min="1" id="capacity" class="form-control" value="{{ $host['capacity'] ?? '' }}">
                            <div class="invalid-feedback">Capacity wajib diisi</div>
                        </div>

                        <!-- Host Type -->
                        <div class="col-md-3">
                            <label for="host_type" class="form-label">Host Type</label>
                            <select name="host_type" id="host_type" class="select2 form-control" required>
                                <option value="">Pilih Host Type</option>
                                @foreach($hostTypes as $type)
                                    <option value="{{ $type }}" {{ ($maktab->type == $type) ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Host Type wajib dipilih</div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2" required>{{ $host['address'] ?? '' }}</textarea>
                            <div class="invalid-feedback">Address wajib diisi</div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-linear" id="btnSubmit" type="submit">
                                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        const $phone = $('#phone');

        if (!$phone.val().startsWith('62')) {
            $phone.val('62');
        }

        $phone.on('input', function () {
            let val = $(this).val().replace(/\D/g, '');
            if (!val.startsWith('62')) {
                val = '62';
            }
            $(this).val(val);
        });

        $phone.on('keydown', function (e) {
            const cursorPos = this.selectionStart;
            if ((cursorPos <= 2) && (e.key === 'Backspace' || e.key === 'Delete')) {
                e.preventDefault();
            }
        });

        function fetchCoordinatorBySector(sectorId) {
            const url = `{{ route('admin.maktabs.coordinator_sektor', ['id' => '__ID__']) }}`.replace('__ID__', sectorId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (res) {
                    $('#coordinator_id').val(res.id).trigger('change');
                    $('#cp_name').val(res.name);
                    $('#cp_phone').val(res.phone);
                },
                error: function () {
                    $('#coordinator_id').val('').trigger('change');
                    $('#cp_name').val('');
                    $('#cp_phone').val('');
                }
            });
        }

        // Fungsi untuk set CP berdasarkan coordinator ID
        function fetchCoordinatorData(id) {
            const url = `{{ route('admin.maktab.coor-data', ['id' => '__ID__']) }}`.replace('__ID__', id);

            $.get(url, function (res) {
                $('#cp_name').val(res.name);
                $('#cp_phone').val(res.phone);
            }).fail(function () {
                $('#cp_name').val('');
                $('#cp_phone').val('');
            });
        }

        // Trigger saat sector diubah
        $('#sector_id').on('change', function () {
            const sectorId = $(this).val();
            if (sectorId) {
                fetchCoordinatorBySector(sectorId);
            } else {
                $('#coordinator_id').val('').trigger('change');
                $('#cp_name').val('');
                $('#cp_phone').val('');
            }
        });

        // Trigger saat coordinator dipilih manual
        $('#coordinator_id').on('change', function () {
            const id = $(this).val();
            if (id) {
                fetchCoordinatorData(id);
            } else {
                $('#cp_name').val('');
                $('#cp_phone').val('');
            }
        });

        // === INISIALISASI SAAT HALAMAN EDIT ===

        // Cek jika sektor sudah terisi saat halaman load
        const initialSector = $('#sector_id').val();
        const initialCoordinator = $('#coordinator_id').val();

        if (initialSector) {
            fetchCoordinatorBySector(initialSector);
        } else if (initialCoordinator) {
            // Jika tidak ada sektor tapi ada koordinator
            fetchCoordinatorData(initialCoordinator);
        }

        $('#formMaktab').on('submit', function (e) {
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
                        window.location.href = "{{ route('admin.maktabs.index') }}";
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