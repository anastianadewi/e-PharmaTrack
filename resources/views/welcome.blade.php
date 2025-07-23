<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selamat Datang - PharmaTrack</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      background: linear-gradient(135deg, #dfe8ff, #b6c9f9);
      color: #1c1d52;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      text-align: center;
      padding: 0 20px;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
      top: -150px;
      left: -150px;
      z-index: 0;
    }

    body::after {
      content: "";
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 80%);
      bottom: -150px;
      right: -150px;
      z-index: 0;
    }

    .logo {
      width: 90px;
      margin-bottom: 24px;
      z-index: 2;
    }

    h1 {
      font-size: 44px;
      font-weight: 600;
      margin-bottom: 16px;
      z-index: 2;
    }

    p {
      font-size: 18px;
      color: #333;
      margin-bottom: 40px;
      z-index: 2;
    }

    .btn-start {
      background: #1c1d52;
      color: white;
      padding: 14px 36px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      transition: 0.3s ease;
      z-index: 2;
    }

    .btn-start:hover {
      background-color: #13143a;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }

    .manual-top-right {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #1c1d52;
      color: white;
      padding: 10px 14px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 6px;
      text-decoration: none;
      z-index: 10;
      transition: 0.3s ease;
    }

    .manual-top-right:hover {
      background-color: #13143a;
      box-shadow: 0 6px 18px rgba(0,0,0,0.2);
      transform: translateY(-1px);
    }

    .manual-dropdown {
      display: none;
      position: absolute;
      top: 54px;
      right: 20px;
      background: white;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      z-index: 11;
      min-width: 180px;
    }

    .manual-dropdown a {
      display: block;
      padding: 10px 16px;
      color: #1c1d52;
      text-decoration: none;
      font-weight: 500;
    }

    .manual-dropdown a:hover {
      background-color: #f0f4ff;
    }

    @media (max-width: 768px) {
      h1 {
        font-size: 34px;
      }

      p {
        font-size: 16px;
      }

      .btn-start {
        padding: 12px 28px;
      }
    }
  </style>
</head>
<body>
  <a href="#" class="manual-top-right" onclick="toggleManualDropdown(event)">📘 Manual Book</a>
  <div class="manual-dropdown" id="manualDropdown">
    <a href="{{ asset('manual/manual_nakes.pdf') }}" target="_blank">Manual Book Nakes</a>
    <a href="{{ asset('manual/manual_bmn.pdf') }}" target="_blank">Manual Book BMN</a>
    <a href="{{ asset('manual/manual_kepala.pdf') }}" target="_blank">Manual Book Kepala</a>
  </div>

  <img src="{{ asset('images/logo_kemenkumham.jpg') }}" alt="Logo Kemenkumham" class="logo">
  <h1>Selamat Datang di e-PharmaTrack</h1>
  <p>Sistem Informasi Pengelolaan Obat<br>Klinik Rutan Kelas IIB Tamiang Layang</p>
  <button class="btn-start" onclick="goToLogin()">Masuk ke Aplikasi</button>

  <script>
    function goToLogin() {
      document.body.style.opacity = '0';
      setTimeout(() => {
        window.location.href = "{{ route('login') }}";
      }, 500);
    }
    function toggleManualDropdown(event) {
      event.preventDefault();
      const dropdown = document.getElementById('manualDropdown');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('manualDropdown');
      const button = document.querySelector('.manual-top-right');
      if (!dropdown.contains(event.target) && !button.contains(event.target)) {
        dropdown.style.display = 'none';
      }
    });
  </script>

</body>
</html>
