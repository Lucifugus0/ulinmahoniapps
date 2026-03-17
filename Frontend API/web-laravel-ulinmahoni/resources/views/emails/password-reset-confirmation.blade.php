<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Successful - Ulin Mahoni</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px 0;
        }
        .language-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        .language-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .language-label {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            color: #666;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .success-banner {
            background-color: #10B981;
            color: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
        }
        .info-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #10B981;
            margin: 20px 0;
        }
        .warning-box {
            background-color: #FEF3C7;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #F59E0B;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/assets/ulinmahoni-logo.png') }}" alt="Ulin Mahoni" class="logo">
        <h1>Password Reset Successful / Reset Kata Sandi Berhasil</h1>
    </div>

    <div class="content">
        <!-- English Version -->
        <div class="language-section">
            <span class="language-label">English</span>

            <div class="success-banner">
                ✓ Your password has been successfully reset
            </div>

            <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>

            <p>This email confirms that your password for your Ulin Mahoni account has been successfully reset.</p>

            <div class="info-box">
                <strong>Reset Details:</strong><br>
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Date & Time:</strong> {{ $resetTime }}
            </div>

            <p>You can now log in to your account using your new password.</p>

            <div class="warning-box">
                <strong>⚠ Didn't make this change?</strong><br>
                If you did not reset your password, please contact our support team immediately at <strong>support@ulinmahoni.com</strong> to secure your account.
            </div>

            <p>For security purposes, we recommend that you:</p>
            <ul>
                <li>Keep your password private and secure</li>
                <li>Use a strong, unique password</li>
                <li>Never share your password with anyone</li>
            </ul>
        </div>

        <!-- Indonesian Version -->
        <div class="language-section">
            <span class="language-label">Bahasa Indonesia</span>

            <div class="success-banner">
                ✓ Kata sandi Anda telah berhasil direset
            </div>

            <p>Halo {{ $user->first_name }} {{ $user->last_name }},</p>

            <p>Email ini mengonfirmasi bahwa kata sandi untuk akun Ulin Mahoni Anda telah berhasil direset.</p>

            <div class="info-box">
                <strong>Detail Reset:</strong><br>
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Tanggal & Waktu:</strong> {{ $resetTime }}
            </div>

            <p>Anda sekarang dapat masuk ke akun Anda menggunakan kata sandi baru Anda.</p>

            <div class="warning-box">
                <strong>⚠ Tidak melakukan perubahan ini?</strong><br>
                Jika Anda tidak mereset kata sandi Anda, segera hubungi tim dukungan kami di <strong>support@ulinmahoni.com</strong> untuk mengamankan akun Anda.
            </div>

            <p>Untuk tujuan keamanan, kami merekomendasikan agar Anda:</p>
            <ul>
                <li>Menjaga kerahasiaan dan keamanan kata sandi Anda</li>
                <li>Menggunakan kata sandi yang kuat dan unik</li>
                <li>Jangan pernah membagikan kata sandi Anda kepada siapa pun</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Ulin Mahoni. All rights reserved. / Hak cipta dilindungi.</p>
        <p>This is an automated email, please do not reply. / Ini adalah email otomatis, mohon untuk tidak membalas.</p>
    </div>
</body>
</html>
