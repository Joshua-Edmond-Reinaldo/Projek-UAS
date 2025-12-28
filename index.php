<?php
session_start();
// Data Produk (Simulasi Database)
$products = [
    [
        'name' => 'Antivirus Pro 2025',
        'desc' => 'Perlindungan maksimal dari ancaman siber terbaru.',
        'price' => 250000,
        'features' => ['Real-time Protection', 'Anti-Ransomware', 'VPN Included']
    ],
    [
        'name' => 'Video Editor X',
        'desc' => 'Edit video profesional dengan mudah dan cepat.',
        'price' => 750000,
        'features' => ['4K Rendering', 'AI Effects', 'Stock Library']
    ],
    [
        'name' => 'Cloud ERP System',
        'desc' => 'Kelola bisnis Anda dari mana saja secara real-time.',
        'price' => 1500000,
        'features' => ['Finance & HR Modul', 'Unlimited Users', 'Daily Backup']
    ],
    [
        'name' => 'Dev Tools Ultimate',
        'desc' => 'Paket lengkap untuk developer full-stack.',
        'price' => 500000,
        'features' => ['IDE License', 'Database Tools', 'API Tester']
    ]
];

// Logika Pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtered_products = [];

if ($search) {
    $search_lower = strtolower($search);
    foreach ($products as $p) {
        if (strpos(strtolower($p['name']), $search_lower) !== false || strpos(strtolower($p['desc']), $search_lower) !== false) {
            $filtered_products[] = $p;
        }
    }
} else {
    $filtered_products = $products;
}

// Hitung item di keranjang
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) { $cart_count += $item['qty']; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberSoft Store - Software Masa Depan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Navbar */
        .navbar {
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(80, 250, 123, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.5em;
            font-weight: 700;
            color: #50fa7b;
            text-decoration: none;
            text-shadow: 0 0 10px rgba(80, 250, 123, 0.3);
        }

        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover { color: #50fa7b; }
        .btn-login {
            padding: 8px 20px;
            border: 1px solid #bd93f9;
            border-radius: 8px;
            color: #bd93f9 !important;
        }
        .btn-login:hover {
            background: #bd93f9;
            color: #0f0f23 !important;
            box-shadow: 0 0 15px rgba(189, 147, 249, 0.4);
        }
        
        .cart-icon {
            position: relative;
            margin-left: 20px;
            font-size: 1.2em;
        }
        .cart-badge {
            position: absolute;
            top: -8px; right: -10px;
            background: #ff5555; color: white;
            font-size: 0.7em; padding: 2px 6px;
            border-radius: 50%;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: radial-gradient(circle at center, rgba(80, 250, 123, 0.1) 0%, transparent 70%);
        }

        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #50fa7b, #8be9fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(80, 250, 123, 0.2);
        }

        .hero p {
            font-size: 1.2em;
            color: #bd93f9;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        /* Products Grid */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 50px;
            color: #f8f8f2;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60%;
            height: 4px;
            background: #ff79c6;
            margin: 10px auto 0;
            border-radius: 2px;
            box-shadow: 0 0 10px #ff79c6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .card {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .card:nth-child(1) { animation-delay: 0.2s; }
        .card:nth-child(2) { animation-delay: 0.4s; }
        .card:nth-child(3) { animation-delay: 0.6s; }
        .card:nth-child(4) { animation-delay: 0.8s; }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: #50fa7b;
        }

        .card h3 {
            margin-top: 0;
            color: #8be9fd;
            font-size: 1.5em;
        }

        .price {
            font-size: 1.8em;
            font-weight: 700;
            color: #50fa7b;
            margin: 20px 0;
            text-shadow: 0 0 10px rgba(80, 250, 123, 0.2);
        }

        .features {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
            color: #e2e8f0;
            font-size: 0.9em;
        }

        .features li { margin-bottom: 10px; }
        .features li::before { content: '✓ '; color: #ff79c6; }

        .btn-buy {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            border-radius: 12px;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-buy:hover {
            box-shadow: 0 0 20px rgba(80, 250, 123, 0.4);
            transform: scale(1.02);
        }

        /* Search Form */
        .search-wrapper {
            text-align: center;
            margin-bottom: 40px;
        }
        .search-input {
            padding: 12px 20px;
            width: 300px;
            border-radius: 25px;
            border: 1px solid #bd93f9;
            background: rgba(15, 15, 35, 0.8);
            color: #fff;
            outline: none;
        }
        .search-btn {
            padding: 12px 25px;
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #bd93f9, #ff79c6);
            color: #0f0f23;
            font-weight: bold;
            cursor: pointer;
            margin-left: 10px;
        }

        /* About Us Section */
        .about-us-section {
            padding: 80px 0;
            background: rgba(15, 15, 35, 0.8);
            border-top: 1px solid rgba(80, 250, 123, 0.1);
        }

        .about-us-section .container {
            text-align: center;
        }

        .about-us-section p {
            max-width: 800px;
            margin: 0 auto;
            color: #c4c9d4;
            line-height: 1.8;
            font-size: 1.1em;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-top: 1px solid rgba(80, 250, 123, 0.1);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            font-size: 4em;
            color: rgba(80, 250, 123, 0.2);
            position: absolute;
            top: 10px;
            left: 20px;
            font-family: serif;
        }

        .testimonial-text {
            font-style: italic;
            color: #e2e8f0;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .testimonial-author {
            color: #50fa7b;
            font-weight: bold;
            text-align: right;
        }

        /* FAQ Section */
        .faq-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            border-top: 1px solid rgba(80, 250, 123, 0.1);
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        details {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        details:hover {
            border-color: #50fa7b;
            box-shadow: 0 5px 15px rgba(80, 250, 123, 0.1);
        }

        summary {
            padding: 20px;
            cursor: pointer;
            font-weight: 600;
            color: #e2e8f0;
            list-style: none;
            position: relative;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        summary::after {
            content: '+';
            position: absolute;
            right: 20px;
            color: #50fa7b;
            font-weight: bold;
            font-size: 1.2em;
        }

        details[open] summary::after {
            content: '-';
            color: #ff5555;
        }

        .faq-answer {
            padding: 20px;
            color: #a0a0b0;
            line-height: 1.6;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 20px;
            background: rgba(15, 15, 35, 0.8);
            border-top: 1px solid rgba(80, 250, 123, 0.1);
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #bd93f9;
            font-weight: 600;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #f8f8f2;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #50fa7b;
            box-shadow: 0 0 10px rgba(80, 250, 123, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(80, 250, 123, 0.3);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 40px 20px;
            background: rgba(15, 15, 35, 0.95);
            border-top: 1px solid rgba(80, 250, 123, 0.1);
            margin-top: 50px;
        }

        .social-links {
            margin-bottom: 20px;
        }

        .social-links a {
            color: #bd93f9;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1em;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #50fa7b;
            text-shadow: 0 0 10px rgba(80, 250, 123, 0.4);
        }

        .footer p {
            color: #6272a4;
            font-size: 0.9em;
            margin: 0;
        }

        /* Newsletter Form */
        .newsletter-form {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
        }

        .newsletter-form form {
            display: flex;
            gap: 10px;
        }
        
        .newsletter-input {
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #bd93f9;
            background: rgba(15, 15, 35, 0.8);
            color: #fff;
            outline: none;
            width: 250px;
        }

        .newsletter-btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            font-weight: bold;
            cursor: pointer;
        }

        /* Back to Top Button */
        #backToTop {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            font-size: 18px;
            border: none;
            outline: none;
            background: linear-gradient(135deg, #bd93f9, #ff79c6);
            color: #0f0f23;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(189, 147, 249, 0.4);
            transition: all 0.3s ease;
        }

        #backToTop:hover {
            background: linear-gradient(135deg, #ff79c6, #bd93f9);
            transform: translateY(-5px);
            box-shadow: 0 0 25px rgba(255, 121, 198, 0.6);
        }

        /* Cookie Banner */
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(15, 15, 35, 0.95);
            border-top: 1px solid #50fa7b;
            padding: 20px;
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            gap: 20px;
            z-index: 1000;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.5);
        }
        
        .cookie-text {
            color: #e2e8f0;
            font-size: 0.9em;
        }
        
        .cookie-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .cookie-btn:hover {
            box-shadow: 0 0 15px rgba(80, 250, 123, 0.4);
        }

        /* Chat Widget */
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 100px;
            z-index: 99;
            font-size: 24px;
            border: none;
            outline: none;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            cursor: pointer;
            padding: 0;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(80, 250, 123, 0.4);
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 25px rgba(80, 250, 123, 0.6);
        }

        .chat-window {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 320px;
            height: 400px;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 16px;
            border: 1px solid rgba(80, 250, 123, 0.3);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            z-index: 100;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            padding: 15px;
            color: #0f0f23;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-close {
            cursor: pointer;
            font-size: 1.2em;
        }

        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            padding: 8px 12px;
            border-radius: 10px;
            max-width: 80%;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .message.received {
            background: rgba(255, 255, 255, 0.1);
            align-self: flex-start;
            color: #e2e8f0;
            border-bottom-left-radius: 2px;
        }

        .message.sent {
            background: rgba(80, 250, 123, 0.2);
            align-self: flex-end;
            color: #50fa7b;
            border: 1px solid rgba(80, 250, 123, 0.2);
            border-bottom-right-radius: 2px;
        }

        .chat-input-area {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 10px;
            background: rgba(0, 0, 0, 0.2);
        }

        .chat-input {
            flex: 1;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            outline: none;
            font-size: 0.9em;
        }
        
        .chat-input:focus {
            border-color: #50fa7b;
        }

        .chat-send {
            background: none;
            border: none;
            color: #50fa7b;
            cursor: pointer;
            font-size: 1.2em;
            padding: 0 5px;
            transition: transform 0.2s;
        }
        
        .chat-send:hover {
            transform: scale(1.1);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero, .section-title, .about-us-section, .testimonials-section, .faq-section, .contact-section {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">CyberSoft</a>
        <div class="nav-links">
            <a href="#products">Produk</a>
            <a href="#about">Tentang Kami</a>
            <a href="#faq">FAQ</a>
            <a href="#contact">Kontak</a>
            <a href="login.php" class="btn-login">Admin Login</a>
            <a href="keranjang.php" class="cart-icon">
                🛒 <?php if($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?>
            </a>
        </div>
    </nav>

    <header class="hero">
        <h1>Upgrade Digital Life Anda</h1>
        <p>Solusi software terbaik untuk produktivitas, keamanan, dan kreativitas tanpa batas.</p>
    </header>

    <div class="container" id="products">
        <h2 class="section-title">Pilihan Software Unggulan</h2>
        
        <div class="search-wrapper">
            <form action="" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Cari software..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn">Cari</button>
            </form>
        </div>

        <div class="grid">
            <?php if (empty($filtered_products)): ?>
                <p style="text-align:center; grid-column: 1/-1; color: #ffb86c;">Produk tidak ditemukan.</p>
            <?php else: ?>
                <?php foreach ($filtered_products as $p): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p style="color: #a0a0b0;"><?= htmlspecialchars($p['desc']) ?></p>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <ul class="features">
                            <?php foreach ($p['features'] as $f): ?>
                                <li><?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="tambah_keranjang.php?software=<?= urlencode($p['name']) ?>&price=<?= $p['price'] ?>" class="btn-buy">Tambah ke Keranjang</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <section class="about-us-section" id="about">
        <div class="container">
            <h2 class="section-title">Tentang CyberSoft</h2>
            <p>
                CyberSoft didirikan pada tahun 2020 dengan misi untuk menyediakan solusi perangkat lunak yang inovatif, aman, dan andal bagi individu maupun bisnis. Kami percaya bahwa teknologi yang tepat dapat membuka potensi tak terbatas. Tim kami terdiri dari para ahli yang bersemangat dalam menciptakan produk yang tidak hanya fungsional tetapi juga mudah digunakan. Dari keamanan siber hingga alat kreativitas, kami berkomitmen untuk memberikan yang terbaik bagi pelanggan kami di seluruh dunia.
            </p>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">Apa Kata Mereka?</h2>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"Antivirus Pro 2025 benar-benar menyelamatkan data perusahaan kami dari serangan ransomware. Sangat direkomendasikan!"</p>
                    <div class="testimonial-author">- Budi Santoso, CEO TechIndo</div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"Video Editor X sangat ringan namun fiturnya setara dengan software editing profesional mahal lainnya. Rendering 4K super cepat!"</p>
                    <div class="testimonial-author">- Sarah Wijaya, Content Creator</div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"Cloud ERP System membantu saya mengelola stok dan keuangan toko cabang dengan sangat mudah dari mana saja."</p>
                    <div class="testimonial-author">- Andi Pratama, Pemilik Retail</div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-section" id="faq">
        <div class="container faq-container">
            <h2 class="section-title">Pertanyaan Umum (FAQ)</h2>
            
            <details>
                <summary>Bagaimana cara membeli software?</summary>
                <div class="faq-answer">
                    Pilih software yang Anda inginkan, klik tombol "Beli Sekarang", isi formulir pemesanan, dan lakukan pembayaran sesuai instruksi yang diberikan.
                </div>
            </details>

            <details>
                <summary>Apakah lisensi berlaku selamanya?</summary>
                <div class="faq-answer">
                    Tergantung pada jenis software. Sebagian besar produk kami menawarkan lisensi seumur hidup (lifetime), namun ada juga yang berbasis langganan tahunan. Cek detail pada deskripsi produk.
                </div>
            </details>

            <details>
                <summary>Metode pembayaran apa saja yang tersedia?</summary>
                <div class="faq-answer">
                    Kami menerima pembayaran melalui Transfer Bank, Kartu Kredit, E-Wallet (OVO, GoPay, Dana), dan QRIS.
                </div>
            </details>

            <details>
                <summary>Apakah ada garansi uang kembali?</summary>
                <div class="faq-answer">
                    Ya, kami memberikan garansi uang kembali 30 hari jika software tidak berfungsi sesuai dengan spesifikasi yang dijanjikan.
                </div>
            </details>

            <details>
                <summary>Bagaimana jika saya mengalami kendala teknis?</summary>
                <div class="faq-answer">
                    Tim support kami siap membantu 24/7. Anda bisa menghubungi kami melalui halaman Kontak atau fitur live chat jika tersedia.
                </div>
            </details>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="container">
            <h2 class="section-title">Hubungi Kami</h2>
            <div class="contact-form">
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required placeholder="Masukkan nama Anda">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="newsletter-form">
            <form action="#" method="POST">
                <input type="email" name="newsletter_email" class="newsletter-input" placeholder="Langganan Newsletter..." required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
        <div class="social-links">
            <a href="#" target="_blank">Instagram</a>
            <a href="#" target="_blank">Twitter</a>
            <a href="#" target="_blank">Facebook</a>
            <a href="#" target="_blank">LinkedIn</a>
        </div>
        <p>&copy; <?= date('Y') ?> CyberSoft Store. All rights reserved.</p>
    </footer>

    <button onclick="topFunction()" id="backToTop" title="Kembali ke Atas">⬆️</button>

    <!-- Chat Widget -->
    <button class="chat-button" onclick="toggleChat()">💬</button>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <span>Live Support</span>
            <span class="chat-close" onclick="toggleChat()">✖</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message received">Halo! Ada yang bisa kami bantu?</div>
        </div>
        <div class="chat-input-area">
            <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan..." onkeypress="handleChatKey(event)">
            <button class="chat-send" onclick="sendMessage()">➤</button>
        </div>
    </div>

    <!-- Cookie Banner -->
    <div id="cookieBanner" class="cookie-banner">
        <div class="cookie-text">
            Kami menggunakan cookie untuk meningkatkan pengalaman Anda. Dengan melanjutkan, Anda menyetujui penggunaan cookie kami.
        </div>
        <button id="acceptCookie" class="cookie-btn">Saya Setuju</button>
    </div>

    <script>
        // Get the button
        let mybutton = document.getElementById("backToTop");

        // When the user scrolls down 200px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        // Cookie Consent Logic
        const cookieBanner = document.getElementById('cookieBanner');
        const acceptCookieBtn = document.getElementById('acceptCookie');

        if (!localStorage.getItem('cookieConsent')) {
            cookieBanner.style.display = 'flex';
        }

        acceptCookieBtn.addEventListener('click', () => {
            localStorage.setItem('cookieConsent', 'true');
            cookieBanner.style.display = 'none';
        });

        // Chat Widget Logic
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            if (chatWindow.style.display === 'flex') {
                chatWindow.style.display = 'none';
            } else {
                chatWindow.style.display = 'flex';
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const messages = document.getElementById('chatMessages');
            const text = input.value.trim();

            if (text) {
                // Add user message
                const userMsg = document.createElement('div');
                userMsg.className = 'message sent';
                userMsg.textContent = text;
                messages.appendChild(userMsg);
                
                input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // Simulate reply
                setTimeout(() => {
                    const replyMsg = document.createElement('div');
                    replyMsg.className = 'message received';
                    replyMsg.textContent = "Terima kasih atas pesan Anda. Tim kami akan segera merespons.";
                    messages.appendChild(replyMsg);
                    messages.scrollTop = messages.scrollHeight;
                }, 1000);
            }
        }

        function handleChatKey(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }
    </script>

</body>
</html>
</html>