<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password - Ulin Mahoni</title>
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
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #10B981;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #10B981;
            margin: 20px 0;
            word-break: break-all;
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
        <h1>Reset Your Password / Reset Kata Sandi Anda</h1>
    </div>

    <div class="content">
        <!-- English Version -->
        <div class="language-section">
            <span class="language-label">English</span>
            <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>
            <p>You are receiving this email because we received a password reset request for your Ulin Mahoni account.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>

            <p>Or copy and paste the following URL into your browser:</p>
            <div class="info-box">
                {{ $resetUrl }}
            </div>

            <div class="warning-box">
                <strong>⚠ Important:</strong><br>
                This password reset link will expire in <strong>1 hour</strong>.
            </div>

            <p>If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>

            <p>For security purposes:</p>
            <ul>
                <li>Never share this link with anyone</li>
                <li>Do not forward this email</li>
                <li>If you suspect unauthorized access, contact our support team immediately</li>
            </ul>
        </div>

        <!-- Indonesian Version -->
        <div class="language-section">
            <span class="language-label">Bahasa Indonesia</span>
            <p>Halo {{ $user->first_name }} {{ $user->last_name }},</p>
            <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Ulin Mahoni Anda.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Kata Sandi</a>
            </div>

            <p>Atau salin dan tempel URL berikut ke browser Anda:</p>
            <div class="info-box">
                {{ $resetUrl }}
            </div>

            <div class="warning-box">
                <strong>⚠ Penting:</strong><br>
                Link reset kata sandi ini akan kedaluwarsa dalam <strong>1 jam</strong>.
            </div>

            <p>Jika Anda tidak meminta reset kata sandi, abaikan email ini. Kata sandi Anda tidak akan berubah.</p>

            <p>Untuk keamanan:</p>
            <ul>
                <li>Jangan pernah membagikan link ini kepada siapa pun</li>
                <li>Jangan meneruskan email ini</li>
                <li>Jika Anda mencurigai akses tidak sah, segera hubungi tim dukungan kami</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Ulin Mahoni. All rights reserved. / Hak cipta dilindungi.</p>
        <p>This is an automated email, please do not reply. / Ini adalah email otomatis, mohon untuk tidak membalas.</p>
    </div>
</body>
</html>
