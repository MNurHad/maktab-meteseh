<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HAF Meteseh 2025</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/components/core/css/home.css') }}">
    <link href="{{ asset('assets/components/core/img/favicon.ico') }}" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('assets/components/core/img/logo.png') }}" alt="Logo">
            </div>
            {{ env('APP_NAME') }}
        </div>
    </header>
    <div class="container">
        <h1>Cari Lokasi Maktab</h1>
        <p>Cari Berdasarkan ketua rombongan, kota, kecamatan</p>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari maktab anda... 🔍">
        </div>
        <div id="resultContainer" class="d-flex flex-column align-items-center"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            let debounceTimer;

            $('#searchInput').on('input', function () {
                const keyword = $(this).val();
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(() => {
                    if (!keyword.trim()) {
                        $('#resultContainer').empty();
                        return;
                    }

                    $.ajax({
                        url: "{{ route('searchMaktab') }}",
                        method: 'GET',
                        data: { keyword: keyword },
                        success: function (response) {
                            renderResults(response.data || []);
                        },
                        error: function () {
                            $('#resultContainer').html('<p class="text-danger">Gagal memuat data.</p>');
                        }
                    });
                }, 1000);
            });

            function renderResults(data) {
                const container = $('#resultContainer');
                container.empty();

                if (data.length === 0) {
                    container.html('<div class="alert alert-warning">Tidak ditemukan hasil pencarian.</div>');
                    return;
                }

                data.forEach(item => {
                    const isValidWA = (phone) => phone && phone !== '62';
                    const waLink = (phone, message) =>
                        `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(message)}`;

                    const pesanKetua = `Assalamu'alaikum, saya ingin mengkonfirmasi informasi maktab ini kepada Ketua Rombongan.

        📌 *Sektor:* ${item.sektor}
        👤 *Koordinator Sektor:* ${item.koordinator_sektor}
        🧑‍💼 *Tuan Rumah:* ${item.tuan_rumah}
        📍 *Alamat Maktab:* ${item.alamat_maktab}

        🗺️ *Alamat Asal:* ${item.kecamatan}
        🏙️ *Kota Asal:* ${item.kota}
        🌍 *Provinsi:* ${item.provinsi}
        👥 *Jumlah Jama'ah:* ${item.jumlah_jamaah}

        🔗 Akses maktab via: https://maktab.synertia.id`;

                    const cardHtml = `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">${item.sektor}</h5>
                            </div>
                            <div class="card-body">
                                <strong>Koordinator Sektor</strong><br>
                                Koordinator Sektor: ${item.koordinator_sektor}<br>
                                ${isValidWA(item.wa_koordinator) ? `<a class="btn btn-sm btn-outline-success mt-1" href="${waLink(item.wa_koordinator, 'Assalamu’alaikum, saya ingin mengkonfirmasi informasi maktab ini kepada Koordinator Sektor.')}" target="_blank">Chat Koordinator</a>` : ''}
                                <hr>
                                <strong>Informasi Alamat Maktab</strong><br>
                                🧑‍💼 Tuan Rumah: ${item.tuan_rumah}<br>
                                📍 Alamat Maktab: ${item.alamat_maktab}<br>
                                ${isValidWA(item.wa_tuan_rumah) ? `<a class="btn btn-sm btn-outline-warning mt-1" href="${waLink(item.wa_tuan_rumah, 'Assalamu’alaikum, saya ingin mengkonfirmasi informasi alamat dan lokasi maktab kepada Tuan Rumah.')}" target="_blank">Chat Tuan Rumah</a>` : ''}
                                <hr>
                                <strong>Informasi Rombongan</strong><br>
                                Ketua Rombongan: ${item.ketua_rombongan}<br>
                                ${isValidWA(item.wa_ketua) ? `<a class="btn btn-sm btn-outline-info mt-1" href="${waLink(item.wa_ketua, pesanKetua)}" target="_blank">Chat Ketua</a><br>` : ''}
                                <strong>Asal Jamaah</strong><br>
                                Kecamatan: ${item.kecamatan}<br>
                                Kota: ${item.kota}<br>
                                Provinsi: ${item.provinsi}<br>
                                Jumlah Jama'ah: ${item.jumlah_jamaah}<br>
                                <a href="https://maktab.synertia.id" target="_blank">🔗 Kunjungi maktab.synertia.id</a>
                                <hr>
                                <small class="text-muted">Note: Silakan tunjukkan hasil pencarian maktab ini ke panitia atau hubungi koordinator sektor untuk info lebih lanjut.</small>
                            </div>
                        </div>
                    `;

                    container.append(cardHtml);
                });
            }
        });
    </script>
</body>
</html>