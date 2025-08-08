@extends('layouts.cms')
@section('cms_content')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
    <!-- Available Maktab -->
        <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Lokasi Maktab</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="available-maktab">0</h6>
                            <span class="text-muted small pt-2 ps-1">Available</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Not Available Maktab -->
        <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Lokasi Maktab</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-pin-map"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="not-available-maktab">0</h6>
                            <span class="text-muted small pt-2 ps-1">Not Available</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jumlah Jamaah -->
        <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Jama'ah</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-person-fill"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="jumlah-jamaah">0</h6>
                            <span class="text-muted small pt-2 ps-1">On Maktab</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jamaah dan Kendaraan -->
    <div class="row">
        <!-- Jumlah Kendaraan -->
        <div class="col-xxl-12 col-md-12">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Kendaraan</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-bus-front-fill"></i>
                        </div>
                        <div class="ps-3" id="vehicleList">
                            <h6 id="jumlah-kendaraan">0</h6>
                            <span class="text-muted small pt-2 ps-1">On Maktab</span>
                            <div id="list-kendaraan" class="text-muted small mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('cms_js')
<script>
$(document).ready(function() {
    $.ajax({
        url: "{{ route('admin.home.data') }}",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.statusCode === 200) {
                const data = response.data;

                // Perbaikan: ID sesuai dengan HTML
                $('#available-maktab').text(data.published);
                $('#not-available-maktab').text(data.recommended);
                $('#jumlah-jamaah').text(data.jamaah);

                let totalVehicles = 0;
                let htmlVehicleList = '';

                $.each(data.vehicles, function(type, count) {
                    htmlVehicleList += `<div><strong>${type}</strong>: ${count}</div>`;
                    totalVehicles += count;
                });

                $('#jumlah-kendaraan').text(totalVehicles);
                $('#list-kendaraan').html(htmlVehicleList);
            }
        },
        error: function(xhr, status, error) {
            console.error('Gagal memuat data:', error);
        }
    });
});
</script>
@endpush