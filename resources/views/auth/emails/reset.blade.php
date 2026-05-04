<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reset Password - Bengkel Dinamo Awi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #0f172a;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(135deg, #3b82f6, #0ea5e9);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }

        .content h2 {
            color: #1e293b;
            font-size: 20px;
            margin-top: 0;
        }

        .btn-container {
            text-align: center;
            margin: 35px 0;
        }

        .btn {
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
        }

        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }

        .trouble-link {
            word-break: break-all;
            color: #3b82f6;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Bengkel Dinamo Awi</h1>
        </div>

        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Anda menerima email ini karena kami mendapat permintaan untuk mengatur ulang (*reset*) password pada akun
                Anda di sistem Bengkel Dinamo Awi.</p>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Reset Password Sekarang</a>
            </div>

            <p>Link reset password ini hanya akan berlaku selama <strong>60 menit</strong>. Jika Anda merasa tidak
                pernah meminta reset password, abaikan saja email ini. Keamanan akun Anda tetap terjamin.</p>

            <p>Salam hangat,<br><strong>Sistem Admin Bengkel Awi</strong></p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
            <p style="font-size: 12px; color: #64748b;">
                Jika Anda kesulitan mengklik tombol "Reset Password Sekarang", silakan *copy* dan *paste* URL di bawah
                ini ke dalam browser web Anda:<br>
                <a href="{{ $url }}" class="trouble-link">{{ $url }}</a>
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Bengkel Dinamo Awi. Hak Cipta Dilindungi.
        </div>
    </div>
</body>

</html>
