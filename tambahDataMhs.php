<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Mahasiswa</title>
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

        /* Falling Code Effect */
        .falling-code {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -2;
            overflow: hidden;
        }

        .code-column {
            position: absolute;
            top: -100px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: rgba(80, 250, 123, 0.3);
            white-space: nowrap;
            animation: fall linear infinite;
        }

        .code-column:nth-child(odd) {
            animation-duration: 8s;
        }

        .code-column:nth-child(even) {
            animation-duration: 12s;
            animation-delay: 2s;
        }

        @keyframes fall {
            0% {
                transform: translateY(-100px);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
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
            max-width: 650px;
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
            text-align: center;
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.3);
            letter-spacing: 1px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            margin-bottom: 8px;
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
            margin-bottom: 0;
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

        .code-snippet {
            background: linear-gradient(145deg, #0f0f23, #1a1a2e);
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 13px;
            color: #50fa7b;
            border-left: 4px solid #50fa7b;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .code-snippet::before {
            content: '⚡';
            position: absolute;
            top: 16px;
            right: 16px;
            color: #ffb86c;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="falling-code">
        <div class="code-column" style="left: 10%;">function(){</div>
        <div class="code-column" style="left: 20%; animation-delay: 1s;">var data = [];</div>
        <div class="code-column" style="left: 30%; animation-delay: 2s;">if(condition){</div>
        <div class="code-column" style="left: 40%; animation-delay: 0.5s;">console.log();</div>
        <div class="code-column" style="left: 50%; animation-delay: 1.5s;">return true;</div>
        <div class="code-column" style="left: 60%; animation-delay: 2.5s;">for(let i=0;</div>
        <div class="code-column" style="left: 70%; animation-delay: 0.8s;">async function</div>
        <div class="code-column" style="left: 80%; animation-delay: 1.8s;">try{ }catch{}</div>
        <div class="code-column" style="left: 90%; animation-delay: 2.2s;">export default</div>
    </div>
    <div class="container">
        <div class="code-snippet">
            Input Data Securely
        </div>
        <form action="simpanDataMhs.php" method="POST">

            <h2>Form Data Mahasiswa</h2>

        <label>NIM :</label>
        <input type="text" name="nim" maxlength="14" placeholder="e.g., 1234567890" onblur="cekNim()" required>

        <label>Nama :</label>
        <input type="text" id="nama" name="nama" placeholder="e.g., John Doe" onblur="cekNama()" required>

        <label>Umur :</label>
        <input type="number" id="umur" name="umur" placeholder="e.g., 20" onblur="cekUmur()" required>

        <label>Tempat Lahir :</label>
        <input type="text" name="tempat_lahir" placeholder="e.g., Semarang" onblur="cekTempatLahir()" required>

        <label>Tanggal Lahir :</label>
        <input type="date" name="tanggal_lahir" onblur="cekTanggalLahir()" required>

        <label>Jumlah Saudara :</label>
        <input type="number" name="jml_saudara" placeholder="e.g., 2" onblur="cekJmlSaudara()" required>

        <label>Alamat :</label>
        <textarea name="alamat" rows="3" placeholder="Enter your address..." required></textarea>

        <label>Kota :</label>
        <select name="kota" required>
            <option value="">Pilih Kota</option>
            <option value="Jakarta">Jakarta</option>
            <option value="Bandung">Bandung</option>
            <option value="Surabaya">Surabaya</option>
            <option value="Medan">Medan</option>
            <option value="Semarang">Semarang</option>
            <option value="Yogyakarta">Yogyakarta</option>
            <option value="Solo">Solo</option>
            <option value="Malang">Malang</option>
            <option value="Bekasi">Bekasi</option>
            <option value="Tangerang">Tangerang</option>
            <option value="Depok">Depok</option>
            <option value="Bogor">Bogor</option>
            <option value="Cirebon">Cirebon</option>
            <option value="Tasikmalaya">Tasikmalaya</option>
            <option value="Sukabumi">Sukabumi</option>
            <option value="Brebes">Brebes</option>
            <option value="Tegal">Tegal</option>
            <option value="Pekalongan">Pekalongan</option>
            <option value="Kudus">Kudus</option>
            <option value="Demak">Demak</option>
            <option value="Salatiga">Salatiga</option>
            <option value="Magelang">Magelang</option>
            <option value="Purwokerto">Purwokerto</option>
            <option value="Cilacap">Cilacap</option>
            <option value="Palembang">Palembang</option>
            <option value="Lampung">Lampung</option>
            <option value="Pontianak">Pontianak</option>
            <option value="Banjarmasin">Banjarmasin</option>
            <option value="Samarinda">Samarinda</option>
            <option value="Balikpapan">Balikpapan</option>
            <option value="Makassar">Makassar</option>
            <option value="Manado">Manado</option>
            <option value="Denpasar">Denpasar</option>
            <option value="Mataram">Mataram</option>
            <option value="Kupang">Kupang</option>
            <option value="Jayapura">Jayapura</option>
        </select>

        <label>No HP :</label>
        <input type="text" name="no_hp" placeholder="e.g., 08123456789" onblur="cekNoHp()" required>

            <label>Jenis Kelamin :</label>
            <div class="radio-group">
                <label><input type="radio" name="jk" value="Laki - Laki" required> Laki - Laki</label>
                <label><input type="radio" name="jk" value="Perempuan"> Perempuan</label>
            </div>

            <label>Status :</label>
            <div class="radio-group">
                <label><input type="radio" name="status" value="Kawin" required> Kawin</label>
                <label><input type="radio" name="status" value="Belum Kawin"> Belum Kawin</label>
            </div>

        <label>Pilih Hobi :</label>
        <div class="checkbox-group">
            <label><input type="checkbox" name="hobi[]" value="Membaca"> Membaca</label>
            <label><input type="checkbox" name="hobi[]" value="Olahraga"> Olahraga</label>
            <label><input type="checkbox" name="hobi[]" value="Musik"> Musik</label>
            <label><input type="checkbox" name="hobi[]" value="Traveling"> Traveling</label><br>
            <label><input type="checkbox" name="hobi[]" value="Memasak"> Memasak</label>
            <label><input type="checkbox" name="hobi[]" value="Fotografi"> Fotografi</label>
            <label><input type="checkbox" name="hobi[]" value="Bermain Game"> Bermain Game</label>
            <label><input type="checkbox" name="hobi[]" value="Menonton Film"> Menonton Film</label><br>
            <label><input type="checkbox" name="hobi[]" value="Berenang"> Berenang</label>
            <label><input type="checkbox" name="hobi[]" value="Menyanyi"> Menyanyi</label>
            <label><input type="checkbox" name="hobi[]" value="Menari"> Menari</label>
            <label><input type="checkbox" name="hobi[]" value="Belajar Bahasa"> Belajar Bahasa</label><br>
            <label><input type="checkbox" name="hobi[]" value="Berkebun"> Berkebun</label>
            <label><input type="checkbox" name="hobi[]" value="Menggambar"> Menggambar</label>
            <label><input type="checkbox" name="hobi[]" value="Programming"> Programming</label>
            <label><input type="checkbox" name="hobi[]" value="Bersepeda"> Bersepeda</label>
        </div>
        <label>Email :</label>
        <input type="email" name="email" placeholder="e.g., john@example.com" onblur="cekEmail()">

        <label>Password :</label>
        <div style="position: relative;">
            <input type="password" name="pass" id="passInput" placeholder="Enter your password (minimal 6 kata)" onblur="cekPassword()" required style="padding-right: 40px;">
            <span id="toggleInputPass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">👁️</span>
        </div>

        <input type="submit" value="Kirim Data">
    </form>
    </div>

    <script>
        function cekNama() {
            var nama = document.getElementById("nama").value;
            if (nama !== "") {
                var regex = /^[a-zA-Z\s']+$/;
                if (!regex.test(nama)) {
                    alert("Nama hanya boleh huruf, spasi, dan tanda kutip!");
                    document.getElementById("nama").value = "";
                } else {
                    confirm("Apakah nama '" + nama + "' sudah benar?");
                }
            }
        }

        function cekUmur() {
            var umur = document.getElementById("umur").value;
            if (umur !== "") {
                if (!/^\d+$/.test(umur)) {
                    alert("Umur harus berupa angka!");
                    document.getElementById("umur").value = "";
                } else {
                    var tanggalLahir = document.getElementById("tanggal_lahir").value;
                    if (tanggalLahir !== "") {
                        var birthDate = new Date(tanggalLahir);
                        var today = new Date();
                        var age = today.getFullYear() - birthDate.getFullYear();
                        var m = today.getMonth() - birthDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        if (parseInt(umur) !== age) {
                            alert("Umur tidak sesuai dengan tanggal lahir!");
                            document.getElementById("umur").value = "";
                        }
                    }
                }
            }
        }

        function cekNim() {
            var nim = document.getElementById("nim").value;
            if (nim !== "") {
                var regex = /^[0-9]+$/;
                if (!regex.test(nim) || nim.length !== 12) {
                    alert("NIM harus berupa angka dan panjang 12 digit!");
                    document.getElementById("nim").value = "";
                } else {
                    confirm("Apakah NIM '" + nim + "' sudah benar?");
                }
            }
        }

        function cekTempatLahir() {
            var tempatLahir = document.getElementById("tempat_lahir").value;
            if (tempatLahir !== "") {
                var regex = /^[a-zA-Z\s]+$/;
                if (!regex.test(tempatLahir)) {
                    alert("Tempat Lahir hanya boleh huruf!");
                    document.getElementById("tempat_lahir").value = "";
                }
            }
        }

        function cekTanggalLahir() {
            var tanggalLahir = document.getElementById("tanggal_lahir").value;
            if (tanggalLahir !== "") {
                var today = new Date();
                var birthDate = new Date(tanggalLahir);
                if (birthDate > today) {
                    alert("Tanggal Lahir tidak boleh di masa depan!");
                    document.getElementById("tanggal_lahir").value = "";
                } else {
                    var umur = document.getElementById("umur").value;
                    if (umur !== "") {
                        var age = today.getFullYear() - birthDate.getFullYear();
                        var m = today.getMonth() - birthDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        if (parseInt(umur) !== age) {
                            alert("Tanggal lahir tidak sesuai dengan umur!");
                            document.getElementById("tanggal_lahir").value = "";
                        }
                    }
                }
            }
        }

        function cekJmlSaudara() {
            var jmlSaudara = document.querySelector("input[name='jml_saudara']").value;
            if (jmlSaudara !== "") {
                if (!/^\d+$/.test(jmlSaudara)) {
                    alert("Jumlah Saudara harus berupa angka!");
                    document.querySelector("input[name='jml_saudara']").value = "";
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
                    confirm("Apakah email '" + email + "' sudah benar?");
                }
            }
        }

        function cekPassword() {
            var pass = document.querySelector("input[name='pass']").value;
            if (pass !== "") {
                if (pass.length < 6) {
                    alert("Password minimal 6 karakter!");
                    document.querySelector("input[name='pass']").value = "";
                } else {
                    confirm("Apakah password '" + pass + "' sudah benar?");
                }
            }
        }

        const toggleInputPass = document.querySelector('#toggleInputPass');
        const passwordInput = document.querySelector('#passInput');

        toggleInputPass.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>

</body>
</html>