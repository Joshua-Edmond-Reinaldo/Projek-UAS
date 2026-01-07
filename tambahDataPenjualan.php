<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Penjualan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 177, 153, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(80, 250, 123, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            box-shadow:
                0 20px 40px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 900px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            backdrop-filter: blur(10px);
            position: relative;
            animation: slideIn 0.6s ease-out;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c, #bd93f9, #ff79c6);
            border-radius: 20px 20px 0 0;
        }

        h2 {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.3);
            letter-spacing: 1px;
        }

        label {
            margin-bottom: 5px;
            color: #ffb86c;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        input[type=text], input[type=number], input[type=date], input[type=email], input[type=password], textarea, select {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            background: linear-gradient(145deg, #2d2d42, #3a3a52);
            color: #e2e8f0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }



        input:focus, textarea:focus, select:focus {
            border-color: #50fa7b;
            outline: none;
            box-shadow:
                0 0 0 3px rgba(80, 250, 123, 0.1),
                0 8px 25px rgba(80, 250, 123, 0.15);
            transform: translateY(-2px);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }

        option {
            background: #2d2d42;
            color: #e2e8f0;
        }

        .radio-group, .checkbox-group {
            margin-bottom: 20px;
            background: linear-gradient(145deg, #1a1a2e, #2a2a3e);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 184, 108, 0.2);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .radio-group label, .checkbox-group label {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 8px;
            font-weight: 400;
            cursor: pointer;
            color: #e2e8f0;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .radio-group label:hover, .checkbox-group label:hover {
            color: #50fa7b;
        }

        input[type=radio], input[type=checkbox] {
            margin-right: 8px;
            accent-color: #50fa7b;
            transform: scale(1.1);
            cursor: pointer;
        }

        input[type=submit] {
            width: 100%;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            padding: 18px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        input[type=submit]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        input[type=submit]:hover {
            background: linear-gradient(135deg, #40e66b, #50fa7b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
        }

        input[type=submit]:hover::before {
            left: 100%;
        }

        input[type=submit]:active {
            transform: translateY(0);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 25px;
                margin: 10px;
                border-radius: 15px;
            }

            h2 {
                font-size: 24px;
                margin-bottom: 20px;
            }

            input[type=text], input[type=number], input[type=date], input[type=email], textarea, select {
                padding: 14px 16px;
                font-size: 13px;
            }

            .radio-group, .checkbox-group {
                padding: 16px;
            }

            .radio-group label, .checkbox-group label {
                margin-right: 15px;
                margin-bottom: 6px;
                font-size: 13px;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e1e2e;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #40e66b, #ff9f43);
        }

        /* Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .full-width { grid-column: span 2; }
        .section-title {
            grid-column: span 2;
            color: #50fa7b;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.1em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .section-title { grid-column: span 1; }
        }

        /* Header Row Style */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }
        
        .header-row h2 {
            margin: 0;
        }

        .btn-back {
            background: rgba(45, 45, 66, 0.8);
            color: #e2e8f0;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: bold;
            border: 1px solid rgba(99, 102, 241, 0.5);
            transition: all 0.3s;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: #3a3a52;
            border-color: #bd93f9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-row">
            <h2>Form Transaksi Penjualan</h2>
            <a href="dashboard.php" class="btn-back">🏠 Dashboard</a>
        </div>
        <form action="simpanDataPenjualan.php" method="POST">
            <div class="form-grid">
                <!-- Pelanggan -->
                <h3 class="section-title">👤 Data Pelanggan</h3>
                <div>
                    <label>Nama Pembeli</label>
                    <input type="text" id="nama_pembeli" name="nama_pembeli" placeholder="e.g., John Doe" onblur="cekNama()" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" placeholder="e.g., john@example.com" onblur="cekEmail()">
                </div>
                <div>
                    <label>No HP</label>
                    <input type="text" name="no_hp" id="no_hp" placeholder="e.g., 08123456789" onblur="cekNoHp()" required>
                </div>
                <div class="full-width">
                    <label>Alamat Tagihan</label>
                    <textarea name="alamat" rows="2" placeholder="Alamat lengkap..." required></textarea>
                </div>

                <!-- Transaksi -->
                <h3 class="section-title">📦 Detail Produk</h3>
                <div>
                    <label>Nama Software</label>
                    <input type="text" name="nama_software" placeholder="e.g., Antivirus Pro 2025" required>
                </div>
                <div>
                    <label>Jumlah Lisensi</label>
                    <input type="number" id="jumlah_lisensi" name="jumlah_lisensi" placeholder="e.g., 5" required>
                </div>
                <div>
                    <label>Tanggal Transaksi</label>
                    <input type="date" name="tanggal_transaksi" required>
                </div>
                <div>
                    <label>Tipe Lisensi</label>
                    <div class="radio-group" style="padding: 10px;">
                        <label><input type="radio" name="tipe_lisensi" value="Personal" required> Personal</label>
                        <label><input type="radio" name="tipe_lisensi" value="Bisnis"> Bisnis</label>
                    </div>
                </div>
                <div class="full-width">
                    <label>Fitur Tambahan</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fitur_tambahan[]" value="Support 24/7"> Support 24/7</label>
                        <label><input type="checkbox" name="fitur_tambahan[]" value="Cloud Storage"> Cloud Storage</label>
                        <label><input type="checkbox" name="fitur_tambahan[]" value="Backup Otomatis"> Backup Otomatis</label>
                        <label><input type="checkbox" name="fitur_tambahan[]" value="Training User"> Training User</label>
                        <label><input type="checkbox" name="fitur_tambahan[]" value="Custom Domain"> Custom Domain</label>
                        <label><input type="checkbox" name="fitur_tambahan[]" value="API Access"> API Access</label>
                    </div>
                </div>

                <!-- Pembayaran & Keamanan -->
                <h3 class="section-title">💳 Pembayaran & Akun</h3>
                <div>
                    <label>Harga Total (Rp)</label>
                    <input type="number" name="harga" placeholder="e.g., 500000" required>
                </div>
                <div>
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" required>
                        <option value="">Pilih Metode</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Kartu Kredit">Kartu Kredit</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div>
                    <label>Status Pembayaran</label>
                    <select name="status_pembayaran" required>
                        <option value="Lunas">Lunas</option>
                        <option value="Pending">Pending</option>
                        <option value="Batal">Batal</option>
                    </select>
                </div>
            </div>

        <input type="submit" value="Simpan Transaksi">
    </form>
    </div>

    <script>
        function cekNama() {
            var nama = document.getElementById("nama_pembeli").value;
            if (nama !== "") {
                var regex = /^[a-zA-Z\s']+$/;
                if (!regex.test(nama)) {
                    alert("Nama hanya boleh huruf, spasi, dan tanda kutip!");
                    document.getElementById("nama_pembeli").value = "";
                } else {
                    // confirm("Apakah nama '" + nama + "' sudah benar?");
                }
            }
        }

        function cekNoHp() {
            var noHp = document.getElementById("no_hp").value;
            if (noHp !== "") {
                var regex = /^08[0-9]+$/;
                if (!regex.test(noHp) || noHp.length < 10 || noHp.length > 13) {
                    alert("No HP harus dimulai dengan 08 dan berupa angka dengan panjang 10-13 digit!");
                    document.getElementById("no_hp").value = "";
                }
            }
        }

        function cekEmail() {
            var email = document.querySelector("input[name='email']").value;
            if (email !== "") {
                var regex = /^[^\s@]+@[^\s@]+\.com$/;
                if (!regex.test(email)) {
                    alert("Email tidak valid! Email harus berakhiran .com (contoh: nama@domain.com)");
                    document.querySelector("input[name='email']").value = "";
                } else {
                    // confirm("Apakah email '" + email + "' sudah benar?");
                }
            }
        }

    </script>

</body>
</html>