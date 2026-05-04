<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bengkel Dinamo Awi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
            pointer-events: none;
        }

        body::before {
            top: -20%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: rgba(56, 189, 248, 0.15);
        }

        body::after {
            bottom: -20%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: rgba(167, 139, 250, 0.15);
        }

        .login-card {
            background: rgba(30, 41, 59, 0.6) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            z-index: 10;
        }

        .input-group-text,
        .form-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
            padding: 0.8rem 1rem;
        }

        .input-group-text {
            border-right: none !important;
            border-radius: 12px 0 0 12px;
            color: #94a3b8 !important;
        }

        .form-control {
            border-left: none !important;
            border-radius: 0 12px 12px 0;
        }

        .form-control:focus {
            box-shadow: none !important;
            border-color: #38bdf8 !important;
        }

        .form-control:focus+.input-group-text,
        .input-group-text:has(+ .form-control:focus) {
            border-color: #38bdf8 !important;
            color: #38bdf8 !important;
        }

        .eye-icon {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important;
            border-radius: 0 12px 12px 0;
            color: #94a3b8 !important;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .eye-icon:hover {
            color: #f8fafc !important;
        }

        .password-input {
            border-radius: 0 !important;
            border-right: none !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #0ea5e9);
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(56, 189, 248, 0.6);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center">
        <div class="login-card mx-3">

            <div class="text-center mb-5">
                <div class="d-inline-flex justify-content-center align-items-center mb-3"
                    style="width: 70px; height: 70px; border-radius: 20px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3);">
                    <i class="fas fa-tools fa-2x text-info"></i>
                </div>
                <h4 class="fw-bold mb-1 tracking-tight">Bengkel Dinamo Awi</h4>
                <p class="text-muted small">Silakan masuk ke sistem Anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary mb-2" style="letter-spacing: 0.5px;">EMAIL
                        ADDRESS</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required
                            autofocus placeholder="admin@bengkelawi.com">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-semibold text-secondary mb-0"
                            style="letter-spacing: 0.5px;">PASSWORD</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="small text-info text-decoration-none fw-semibold">Lupa Password?</a>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control password-input"
                            required placeholder="••••••••">
                        <span class="input-group-text eye-icon" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="form-check">
                        <input class="form-check-input border-secondary bg-transparent" type="checkbox" name="remember"
                            id="remember">
                        <label class="form-check-label small text-muted pt-1" for="remember">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> LOGIN
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('passwordInput');
            const eye = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
                eye.classList.add('text-info');
            } else {
                pwd.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.remove('text-info');
                eye.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Otentikasi Gagal!',
                    html: `
                            <div class="text-center text-danger mb-0">
                                @foreach($errors->all() as $error)
                                    <p class="mb-1 fw-bold">{{ $error }}</p>
                                @endforeach
                            </div>
                        `,
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#f8fafc',
                    borderRadius: '16px'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#f8fafc',
                    borderRadius: '16px'
                });
            @endif

            @if(session('status'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{{ session('status') }}",
                    confirmButtonColor: '#3b82f6',
                    background: '#1e293b',
                    color: '#f8fafc',
                    borderRadius: '16px'
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10b981',
                    background: '#1e293b',
                    color: '#f8fafc',
                    borderRadius: '16px'
                });
            @endif

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function () {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                        btn.classList.add('disabled');
                        btn.style.pointerEvents = 'none';
                    }
                });
            }
        });
    </script>

</body>

</html>
