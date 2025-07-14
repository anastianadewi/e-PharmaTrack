<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - PharmaTrack</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet" />
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-y: hidden; /* sembunyikan scroll */
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #dfe8ff, #b6c9f9);
  }

  body {
    opacity: 0;
    transition: opacity 0.6s ease;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  body.show {
    opacity: 1;
  }

  .login-container {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 30px 24px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
    max-width: 420px;
    overflow: visible;
    }


  .login-container img {
    width: 80px;
    margin-bottom: 16px;
  }

  .login-container h2 {
    font-size: 28px;
    font-weight: 600;
    color: #1c1d52;
    margin-bottom: 6px;
  }

  .login-container p {
    font-size: 14px;
    color: #555;
    margin-bottom: 32px;
  }

  .form-group {
    text-align: left;
    margin-bottom: 20px;
  }

  .form-group label {
    font-weight: 500;
    margin-bottom: 6px;
    display: block;
    color: #1c1d52;
  }

  .form-group input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  .btn {
    background-color: #1c1d52;
    color: #fff;
    border: none;
    padding: 12px;
    width: 100%;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn:hover {
    background-color: #13143a;
  }

  @media (max-width: 480px) {
    .login-container {
      padding: 32px 20px;
    }

    .login-container h2 {
      font-size: 24px;
    }
  }

  .alert-error {
    background-color: #f8d7da;
    color: #842029;
    padding: 12px 20px;
    border-radius: 8px;
    border: 1px solid #f5c2c7;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(184, 43, 50, 0.3);
    opacity: 0;
    transform: translateY(-10px);
    transition: opacity 0.6s ease, transform 0.6s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease;
    overflow: hidden;
    max-height: 200px;
  }
  
  .alert-error.show {
    opacity: 1;
    transform: translateY(0);
  }
  
  .alert-error.fade-out {
    opacity: 0;
    max-height: 0;
    margin: 0;
    padding: 0;
  }

  .login-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
    transition: all 0.4s ease;
  }
    
</style>
</head>
<body>
  <div class="login-container">
    <img src="{{ asset('images/logo_kemenkumham.jpg') }}" alt="Logo Kemenkumham" />
    <h2>e-PharmaTrack</h2>
    <p>Klinik Rutan Kelas IIB Tamiang Layang</p>

    <div class="login-content">
    @if ($errors->has('login'))
    <div class="alert-error">
        {{ $errors->first('login') }}
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Masukkan username"
          required pattern="\S+"
          autofocus
          oninput="this.value = this.value.replace(/\s/g, '')"
        />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Masukkan password"
          required
        />
      </div>

      <button type="submit" class="btn">Login</button>
    </form>
  </div>

  <!-- Script animasi halaman dan notifikasi -->
  <script>
    window.onload = () => {
        document.body.classList.add('show');
        const alert = document.querySelector('.alert-error');
        if (alert) {
        // Tampilkan dengan efek
        requestAnimationFrame(() => alert.classList.add('show'));

        // Lalu hilangkan
        setTimeout(() => {
            alert.classList.add('fade-out');
        }, 4000);
        setTimeout(() => {
            alert.remove();
        }, 4800);
        }
    };
    </script>

</body>
</html>
