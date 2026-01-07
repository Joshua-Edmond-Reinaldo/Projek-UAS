<?php
// Data Produk (Simulasi Database)
$products = [
    [
        'name' => 'Antivirus Pro 2025',
        'desc' => 'Perlindungan maksimal dari ancaman siber terbaru.',
        'category' => 'Keamanan',
        'price' => 250000,
        'features' => ['Real-time Protection', 'Anti-Ransomware', 'VPN Included'],
        'image' => 'asset/Antivirus-Software.jpeg',
        'details' => 'Antivirus Pro 2025 adalah solusi keamanan siber terdepan yang dirancang untuk melindungi perangkat Anda dari segala jenis ancaman digital. Dengan mesin pemindaian berbasis AI, software ini mampu mengenali pola serangan baru (zero-day attacks) secara real-time tanpa memperlambat kinerja sistem. Dilengkapi dengan fitur Cloud Scanning untuk database virus yang selalu terupdate dan Firewall canggih untuk memblokir intrusi jaringan.',
        'tech_stack' => 'C++ (High Performance Engine), Python (AI/ML Core dengan TensorFlow), Assembly (Low-level Hooks)',
        'function' => 'Melindungi data pribadi dari pencurian, mencegah enkripsi file oleh ransomware, mengamankan transaksi perbankan online, memblokir situs phishing, dan menyediakan VPN untuk privasi internet.'
    ],
    [
        'name' => 'Video Editor X',
        'desc' => 'Edit video profesional dengan mudah dan cepat.',
        'category' => 'Kreativitas',
        'price' => 750000,
        'features' => ['4K Rendering', 'AI Effects', 'Stock Library'],
        'image' => 'asset/video_editor.webp',
        'details' => 'Video Editor X menawarkan antarmuka intuitif dengan fitur editing kelas Hollywood. Mendukung multi-track editing tanpa batas, color grading canggih dengan dukungan LUTs, dan efek visual berbasis AI seperti penghapusan background otomatis dan upscaling video. Software ini dioptimalkan untuk rendering super cepat menggunakan akselerasi hardware.',
        'tech_stack' => 'C++ (Core Processing), CUDA & OpenCL (GPU Acceleration), Qt (Cross-platform GUI), FFmpeg (Media Encoding)',
        'function' => 'Mengedit video hingga resolusi 8K, rendering kecepatan tinggi, compositing efek visual, audio mastering, motion tracking, dan produksi konten multimedia untuk berbagai platform.'
    ],
    [
        'name' => 'Cloud ERP System',
        'desc' => 'Kelola bisnis Anda dari mana saja secara real-time.',
        'category' => 'Bisnis',
        'price' => 1500000,
        'features' => ['Finance & HR Modul', 'Unlimited Users', 'Daily Backup'],
        'image' => 'asset/cloud-erp-software.jpg',
        'details' => 'Sistem ERP berbasis cloud yang mengintegrasikan seluruh operasional bisnis Anda dalam satu dashboard terpusat. Mencakup modul keuangan, SDM, inventaris, hingga rantai pasokan (supply chain). Dibangun dengan arsitektur microservices yang skalabel dan dapat diakses dari perangkat apa pun dengan keamanan enkripsi tingkat bank (AES-256).',
        'tech_stack' => 'PHP (Laravel Framework), React.js (Frontend), MySQL & Redis (Database & Caching), Docker & Kubernetes (Containerization)',
        'function' => 'Otomatisasi proses bisnis end-to-end, manajemen stok real-time, pelaporan keuangan dan pajak otomatis, pengelolaan gaji karyawan (payroll), serta analisis prediktif penjualan.'
    ],
    [
        'name' => 'Dev Tools Ultimate',
        'desc' => 'Paket lengkap untuk developer full-stack.',
        'category' => 'Developer',
        'price' => 500000,
        'features' => ['IDE License', 'Database Tools', 'API Tester'],
        'image' => 'asset/dev-tools.png',
        'details' => 'Kumpulan alat pengembangan software all-in-one yang mencakup IDE cerdas dengan autocompletion berbasis AI, klien database visual universal, dan platform pengujian API yang komprehensif. Dirancang untuk meningkatkan produktivitas developer hingga 300% dengan fitur kolaborasi tim secara real-time dan integrasi Git yang mulus.',
        'tech_stack' => 'Rust (Core Performance), Go (Backend Services), Electron (Cross-platform Desktop App), TypeScript',
        'function' => 'Penulisan dan debugging kode multi-bahasa, manajemen database SQL/NoSQL, pengujian dan dokumentasi API, profiling performa aplikasi, dan manajemen kontainer Docker.'
    ],
    [
        'name' => 'Nexus OS',
        'desc' => 'Sistem Operasi masa depan dengan performa tinggi dan keamanan terjamin.',
        'category' => 'Sistem Operasi',
        'price' => 1200000,
        'features' => ['Hybrid Kernel', 'AI Integrated', 'Zero-Trust Security'],
        'image' => 'asset/system OS.jpg',
        'details' => 'Nexus OS adalah sistem operasi generasi berikutnya yang dibangun dengan arsitektur hybrid kernel revolusioner. Menawarkan stabilitas setara server dengan kemudahan penggunaan desktop modern. Dilengkapi integrasi AI mendalam yang mengoptimalkan alokasi resource CPU/RAM secara otomatis sesuai kebiasaan pengguna, serta lapisan kompatibilitas untuk menjalankan aplikasi legacy.',
        'tech_stack' => 'C (Kernel Base), Rust (Driver & Security Modules), Assembly (Bootloader), Wayland (Display Server)',
        'function' => 'Manajemen perangkat keras tingkat lanjut, menjalankan aplikasi dengan isolasi sandbox, menyediakan antarmuka pengguna yang responsif, manajemen jaringan zero-trust, dan virtualisasi bawaan.'
    ]
];
?>