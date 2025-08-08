@extends('layouts.cms')
@section('cms_title', $title)
@section('cms_content')
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.groups.index') }}">Group</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $title }} Edit</h5>
                    <!-- Custom Styled Validation -->
                     <form id="formMaktab" class="row g-3 needs-validation" action="{{ route('admin.groups.update', [$group->id]) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')
                        <!-- Sektor -->
                        <div class="col-md-3">
                            <label for="sector_id" class="form-label">Sektor</label>
                            <select name="sector_id" id="sector_id" class="select2 form-control" required>
                                <option value="">-- Pilih Sektor --</option>
                                @foreach(listSector() as $sector)
                                    <option value="{{ $sector->id }}">{{ $sector->sektor }}</option>
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
                                    <option value="{{ $coor->id }}">{{ $coor->name }}</option>
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
                        <input type="hidden" id="coordinator_sektor_url" value="{{ route('admin.maktabs.coordinator_sektor', ['id' => '__ID__']) }}">
                        <input type="hidden" id="coordinator_data_url" value="{{ route('admin.maktab.coor-data', ['id' => '__ID__']) }}">
                        <hr>
                        <!-- Address -->
                        <div class="col-md-12">
                            <label for="maktab_id" class="form-label">Maktab Location</label>
                            <select name="maktab_id" id="maktab_id" class="select2 form-control" required>
                                <option value="">-- Pilih maktab --</option>
                            </select>
                            <div class="invalid-feedback">Coordinator wajib diisi</div>
                        </div>

                        <div class="col-md-4">
                            <label for="host_name" class="form-label">Host Name</label>
                            <input type="text" id="host_name" class="form-control" disabled>
                        </div>

                        <div class="col-md-4">
                            <label for="host_phone" class="form-label">Host Phone</label>
                            <input type="text" id="host_phone" class="form-control" disabled>
                        </div>

                        <div class="col-md-4">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" name="capacity" min="1" id="capacity" class="form-control" disabled>
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label">Address Maktab</label>
                            <textarea id="address" class="form-control" rows="2" disabled></textarea>
                        </div>
                        <hr>
                        <h6>Data Jama'ah</h6>
                        <hr>
                        <div class="col-md-3">
                            <label for="leader" class="form-label">Leader Name</label>
                            <input type="text" name="leader" id="leader" class="form-control" required>
                            <div class="invalid-feedback">Leader Name wajib dipilih</div>
                        </div>

                        <div class="col-md-3">
                            <label for="phone" class="form-label">Leader Phone</label>
                            <input type="number" name="phone" min="1" id="phone" class="form-control" required>
                            <div class="invalid-feedback">Leader Phone wajib dipilih</div>
                        </div>

                        <div class="col-md-3">
                            <label for="phone" class="form-label">Arrival Date</label>
                            <input type="date" name="planing_at" id="planing_at" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="phone" class="form-label">Departure Date</label>
                            <input type="date" name="actual_at" id="actual_at" class="form-control">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="province" class="form-label">Provinsi</label>
                            <select name="province" id="province" class="select2 form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach(listProvinces() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="city" class="form-label">Kota</label>
                            <select name="city" id="city" class="select2 form-control" required>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="district" class="form-label">Kecamatan</label>
                            <select name="district" id="district" class="select2 form-control" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="village" class="form-label">Kelurahan / Desa</label>
                            <select name="village" id="village" class="select2 form-control" required>
                                <option value="">-- Pilih Desa --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="vehicle" class="form-label">Kendaraan</label>
                            <select name="vehicle" id="vehicle" class="select2 form-control" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($vehicleTypes as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="jamaah" class="form-label">Jumlah Jama'ah</label>
                            <input type="number" name="jamaah" min="1" id="jamaah" class="form-control" required>
                            <div class="invalid-feedback">Jumlah Jama'ah wajib dipilih</div>
                        </div>

                        <div class="col-md-12">
                            <label for="alamat" class="form-label">Alamat Asal</label>
                            <textarea id="alamat" name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <hr>
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
<script>
    $(document).ready(function () {
        function fillMaktabData($option) {
            $('#host_name').val($option.data('host_name') || '');
            $('#host_phone').val($option.data('host_phone') || '');
            $('#capacity').val($option.data('capacity') || '');
            $('#address').val($option.data('address') || '');
        }

        function resetMaktabFields() {
            $('#host_name').val('');
            $('#host_phone').val('');
            $('#capacity').val('');
            $('#address').val('');
            $('#maktab_id').html('<option value="">-- Pilih maktab --</option>').trigger('change');
        }

        const selectedMaktab = $('#maktab_id').val();
        if (selectedMaktab) {
            const $selected = $('#maktab_id').find('option:selected');
            fillMaktabData($selected);
        }

        $('#maktab_id').on('change', function () {
            const $selected = $(this).find('option:selected');
            fillMaktabData($selected);
        });

        $('#sector_id').on('change', function () {
            const sectorId = $(this).val();
            const maktabUrl = `{{ route('getBySector', ['id' => '__ID__']) }}`.replace('__ID__', sectorId);
            const coordinatorUrl = `{{ route('admin.maktabs.coordinator_sektor', ['id' => '__ID__']) }}`.replace('__ID__', sectorId);

            if (sectorId) {
                $.get(coordinatorUrl, function (res) {
                    $('#coordinator_id').val(res.id).trigger('change');
                    $('#cp_name').val(res.name);
                    $('#cp_phone').val(res.phone);
                }).fail(function () {
                    $('#coordinator_id').val('').trigger('change');
                    $('#cp_name').val('');
                    $('#cp_phone').val('');
                });

                $.get(maktabUrl, function (res) {
                    let options = '<option value="">-- Pilih maktab --</option>';
                    res.forEach(function (maktab) {
                        options += `<option value="${maktab.id}" 
                            data-host_name="${maktab.host_data?.owner || ''}" 
                            data-host_phone="${maktab.host_data?.phone || ''}" 
                            data-capacity="${maktab.host_data?.capacity || ''}" 
                            data-address="${maktab.host_data?.address || ''}">
                            ${maktab.host_data?.owner || ''} / ${maktab.host_data?.address || ''}
                        </option>`;
                    });
                    $('#maktab_id').html(options).trigger('change');
                }).fail(function () {
                    resetMaktabFields();
                });

            } else {
                $('#coordinator_id').val('').trigger('change');
                $('#cp_name').val('');
                $('#cp_phone').val('');
                resetMaktabFields();
            }
        });

        $('#coordinator_id').on('change', function () {
            const id = $(this).val();
            const url = `{{ route('admin.maktab.coor-data', ['id' => '__ID__']) }}`.replace('__ID__', id);

            if (id) {
                $.get(url, function (res) {
                    $('#cp_name').val(res.name);
                    $('#cp_phone').val(res.phone);
                }).fail(function () {
                    $('#cp_name').val('');
                    $('#cp_phone').val('');
                });
            } else {
                $('#cp_name').val('');
                $('#cp_phone').val('');
            }
        });
    });

    $(document).ready(function () {
        $('#province').on('change', function () {
            const provinceCode = $(this).val();
            $('#city').html('<option value="">-- Pilih Kota --</option>');
            $('#district').html('<option value="">-- Pilih Kecamatan --</option>');
            $('#village').html('<option value="">-- Pilih Desa --</option>');

            if (provinceCode) {
                $.get(`{{ route('getCities', ['provinceCode' => '__PROV__']) }}`.replace('__PROV__', provinceCode), function (data) {
                    data.forEach(function (item) {
                        $('#city').append(`<option value="${item.code}">${item.name}</option>`);
                    });
                });
            }
        });

        $('#city').on('change', function () {
            const cityCode = $(this).val();
            $('#district').html('<option value="">-- Pilih Kecamatan --</option>');
            $('#village').html('<option value="">-- Pilih Desa --</option>');

            if (cityCode) {
                $.get(`{{ route('getDistricts', ['cityCode' => '__CITY__']) }}`.replace('__CITY__', cityCode), function (data) {
                    data.forEach(function (item) {
                        $('#district').append(`<option value="${item.code}">${item.name}</option>`);
                    });
                });
            }
        });

        $('#district').on('change', function () {
            const districtCode = $(this).val();
            $('#village').html('<option value="">-- Pilih Desa --</option>');

            if (districtCode) {
                $.get(`{{ route('getVillages', ['districtCode' => '__DIST__']) }}`.replace('__DIST__', districtCode), function (data) {
                    data.forEach(function (item) {
                        $('#village').append(`<option value="${item.code}">${item.name}</option>`);
                    });
                });
            }
        });
    });
</script>
@endpush