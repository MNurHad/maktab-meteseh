<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HAF Meteseh 2025</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/components/core/css/home.css') }}">
</head>
<body>
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('assets/components/core/img/logo.png') }}" alt="Logo">
            </div>
            {{ env('APP_NAME') }}
        </div>
        <nav class="menu">
            <a href="{{ url('/') }}">Maktab</a>
            <a href="">Statistik</a>
        </nav>
    </header>
    <div class="container">
        <h1>Cari Lokasi Maktab</h1>
        <p>Cari Berdasarkan ketua rombongan, kota, kecamatan</p>
        <div class="search-box">
            <input type="text" placeholder="Cari maktab anda...">
            <button disabled>🔍</button>
        </div>
        <div id="resultContainer"></div>
    </div>
</body>
</html>