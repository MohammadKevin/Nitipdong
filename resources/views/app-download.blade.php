<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi NitipDong — Belanja Online Terpercaya</title>
    <meta name="description" content="Download aplikasi NitipDong untuk Android. Belanja online lebih mudah, cepat, dan aman langsung dari genggaman Anda.">
    <link rel="icon" href="{{ asset('img/icon-apps.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cyan: #06b6d4;
            --cyan-dark: #0891b2;
            --navy: #0b1528;
            --navy-mid: #0f2044;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--navy);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Background mesh ── */
        .bg-mesh {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 80% 60% at 10% 0%, rgba(6,182,212,.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 100%, rgba(6,182,212,.12) 0%, transparent 60%);
        }

        /* ── Navbar ── */
        nav {
            position: relative; z-index: 10;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.2rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            backdrop-filter: blur(12px);
        }
        .logo { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
        .logo img { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; }
        .logo span { font-size: 1.1rem; font-weight: 800; color: #fff; }
        .logo span em { color: var(--cyan); font-style: normal; }
        nav a.btn-nav {
            padding: .45rem 1.2rem; border-radius: 8px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
            color: #fff; font-size: .8rem; font-weight: 600; text-decoration: none;
            transition: background .2s;
        }
        nav a.btn-nav:hover { background: rgba(255,255,255,.15); }

        /* ── Hero ── */
        .hero {
            position: relative; z-index: 1;
            display: flex; flex-direction: column; align-items: center;
            text-align: center;
            padding: 5rem 1.5rem 3rem;
            gap: 1.5rem;
        }

        .badge-pill {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .35rem 1rem; border-radius: 999px;
            background: rgba(6,182,212,.15); border: 1px solid rgba(6,182,212,.3);
            font-size: .72rem; font-weight: 700; color: var(--cyan); letter-spacing: .05em; text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 3.6rem);
            font-weight: 900; line-height: 1.1;
            background: linear-gradient(135deg, #fff 40%, var(--cyan) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        .subtitle {
            font-size: 1.05rem; color: rgba(255,255,255,.6); max-width: 480px; line-height: 1.7;
        }

        /* ── Download button ── */
        .btn-download {
            display: inline-flex; align-items: center; gap: .75rem;
            padding: 1rem 2rem; border-radius: 14px;
            background: linear-gradient(135deg, var(--cyan-dark), var(--cyan));
            color: #fff; font-size: 1rem; font-weight: 800; text-decoration: none;
            box-shadow: 0 8px 32px rgba(6,182,212,.35);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-download:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(6,182,212,.5); }
        .btn-download i { font-size: 1.4rem; }
        .btn-download small { display: block; font-size: .7rem; font-weight: 500; opacity: .8; margin-top: .1rem; }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 1.5rem; border-radius: 12px;
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.75); font-size: .85rem; font-weight: 600; text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .btn-secondary:hover { border-color: var(--cyan); color: var(--cyan); }

        .cta-group { display: flex; flex-wrap: wrap; gap: .85rem; justify-content: center; align-items: center; }

        .version-note {
            font-size: .72rem; color: rgba(255,255,255,.35); margin-top: -.5rem;
        }

        /* ── Stats strip ── */
        .stats {
            position: relative; z-index: 1;
            display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;
            padding: 2rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.06);
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .stat { text-align: center; }
        .stat strong { display: block; font-size: 1.6rem; font-weight: 900; color: var(--cyan); }
        .stat span { font-size: .75rem; color: rgba(255,255,255,.45); font-weight: 500; }

        /* ── Features ── */
        .section {
            position: relative; z-index: 1;
            max-width: 900px; margin: 0 auto; padding: 4rem 1.5rem;
        }
        .section-title {
            text-align: center; font-size: 1.6rem; font-weight: 800; margin-bottom: 2.5rem;
        }
        .section-title span { color: var(--cyan); }

        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;
        }
        .feature-card {
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px; padding: 1.5rem;
            transition: border-color .2s, background .2s;
        }
        .feature-card:hover { border-color: rgba(6,182,212,.4); background: rgba(6,182,212,.06); }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(6,182,212,.15); display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: var(--cyan); margin-bottom: 1rem;
        }
        .feature-card h3 { font-size: .9rem; font-weight: 700; margin-bottom: .4rem; }
        .feature-card p { font-size: .8rem; color: rgba(255,255,255,.5); line-height: 1.6; }

        /* ── How to install ── */
        .steps { display: flex; flex-direction: column; gap: 1rem; }
        .step {
            display: flex; align-items: flex-start; gap: 1rem;
            background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07);
            border-radius: 14px; padding: 1.2rem 1.4rem;
        }
        .step-num {
            width: 32px; height: 32px; border-radius: 8px; shrink: 0;
            background: linear-gradient(135deg, var(--cyan-dark), var(--cyan));
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; font-weight: 900; flex-shrink: 0;
        }
        .step h4 { font-size: .9rem; font-weight: 700; margin-bottom: .25rem; }
        .step p { font-size: .78rem; color: rgba(255,255,255,.5); line-height: 1.5; }

        /* ── Bottom CTA ── */
        .bottom-cta {
            position: relative; z-index: 1;
            text-align: center; padding: 4rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .bottom-cta h2 { font-size: 1.8rem; font-weight: 900; margin-bottom: .75rem; }
        .bottom-cta p { color: rgba(255,255,255,.5); margin-bottom: 2rem; font-size: .9rem; }

        /* ── Footer ── */
        footer {
            position: relative; z-index: 1;
            text-align: center; padding: 1.5rem;
            font-size: .72rem; color: rgba(255,255,255,.25);
            border-top: 1px solid rgba(255,255,255,.05);
        }
        footer a { color: var(--cyan); text-decoration: none; }

        @media (max-width: 600px) {
            nav { padding: 1rem; }
            .hero { padding: 3.5rem 1rem 2rem; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    {{-- Navbar --}}
    <nav>
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('img/saksershop-logo.png') }}" alt="NitipDong">
            <span>Nitip<em>Dong</em></span>
        </a>
        <a href="{{ url('/') }}" class="btn-nav">
            <i class="fa-solid fa-globe"></i> Buka Website
        </a>
    </nav>

    {{-- Hero --}}
    <div class="hero">
        <div class="badge-pill">
            <i class="fa-brands fa-android"></i>
            Tersedia untuk Android
        </div>

        <h1>Belanja Online<br>Ada di Genggamanmu</h1>

        <p class="subtitle">
            Download aplikasi NitipDong sekarang dan nikmati pengalaman belanja yang lebih cepat, mudah, dan menyenangkan langsung dari HP Anda.
        </p>

        <div class="cta-group">
            <a href="{{ route('app.download') }}" class="btn-download" id="btn-download-main">
                <i class="fa-brands fa-android"></i>
                <div>
                    Download APK
                    <small>NitipDong v1.0.0 · Android 5.0+</small>
                </div>
            </a>
            <a href="{{ url('/') }}" class="btn-secondary">
                <i class="fa-solid fa-globe"></i>
                Versi Website
            </a>
        </div>

        <p class="version-note">Gratis · Tanpa iklan · Aman & terpercaya</p>
    </div>

    {{-- Stats --}}
    <div class="stats">
        <div class="stat">
            <strong>100%</strong>
            <span>Produk Original</span>
        </div>
        <div class="stat">
            <strong>Rp0</strong>
            <span>Ongkos Kirim</span>
        </div>
        <div class="stat">
            <strong>30 Hari</strong>
            <span>Sesi Login Awet</span>
        </div>
        <div class="stat">
            <strong>24/7</strong>
            <span>Customer Service</span>
        </div>
    </div>

    {{-- Features --}}
    <div class="section">
        <h2 class="section-title">Kenapa Pilih <span>NitipDong</span>?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                <h3>Super Cepat</h3>
                <p>Loading super kilat di bawah 1 detik. Temukan produk dan checkout dalam hitungan detik.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-shield-check"></i></div>
                <h3>100% Aman</h3>
                <p>Transaksi dienkripsi SSL. Garansi uang kembali jika produk tidak sesuai deskripsi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-tags"></i></div>
                <h3>Harga Terbaik</h3>
                <p>Flash sale kilat, kupon diskon, dan voucher gratis ongkir setiap hari untuk semua pengguna.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-heart"></i></div>
                <h3>Wishlist & Keranjang</h3>
                <p>Simpan produk favorit, kelola keranjang, dan pantau status pesanan real-time.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-store"></i></div>
                <h3>Official Store</h3>
                <p>Belanja dari toko-toko terverifikasi resmi dengan jaminan produk asli bergaransi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-bell"></i></div>
                <h3>Notifikasi Pintar</h3>
                <p>Update status pesanan dan info promo terbaru langsung ke aplikasi Anda.</p>
            </div>
        </div>
    </div>

    {{-- How to Install --}}
    <div class="section" style="padding-top: 0;">
        <h2 class="section-title">Cara <span>Install</span> Aplikasi</h2>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div>
                    <h4>Download File APK</h4>
                    <p>Tap tombol "Download APK" di atas. File akan otomatis tersimpan di folder Downloads HP Anda.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div>
                    <h4>Izinkan Instalasi dari Sumber Tidak Dikenal</h4>
                    <p>Buka Pengaturan → Keamanan → aktifkan "Sumber Tidak Dikenal" atau "Install Unknown Apps". Cukup dilakukan sekali.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div>
                    <h4>Buka & Install File APK</h4>
                    <p>Buka file NitipDong.apk dari notifikasi atau folder Downloads, lalu tap "Install".</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div>
                    <h4>Daftar atau Masuk Akun</h4>
                    <p>Buat akun baru atau masuk dengan akun yang sudah ada. Mulai belanja dalam hitungan detik! 🎉</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom CTA --}}
    <div class="bottom-cta">
        <h2>Siap Mulai Belanja?</h2>
        <p>Bergabung bersama ribuan pembeli yang sudah merasakan kemudahan belanja di NitipDong.</p>
        <div class="cta-group">
            <a href="{{ route('app.download') }}" class="btn-download" id="btn-download-bottom">
                <i class="fa-brands fa-android"></i>
                <div>
                    Download Sekarang — Gratis!
                    <small>Android 5.0 ke atas · v1.0.0</small>
                </div>
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <footer>
        <p>© {{ date('Y') }} NitipDong · <a href="{{ url('/') }}">budayakita.com</a> · Dibuat dengan ❤️ di Indonesia</p>
    </footer>
</body>
</html>
