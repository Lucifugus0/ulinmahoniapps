<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification - Ulin Mahoni</title>
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
        <h1>Verify Your Email / Verifikasi Email Anda</h1>
    </div>

    <div class="content">
        <!-- English Version -->
        <div class="language-section">
            <span class="language-label">English</span>
            <p>Hello {{ $user->name }},</p>
            <p>Thank you for registering with Ulin Mahoni. To complete your registration, please verify your email address by clicking the button below:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">Verify Email</a>
            </div>

            <p>Or copy and paste the following URL into your browser:</p>
            <p style="word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px;">
                {{ $verificationUrl }}
            </p>

            <p>If you did not make this request, you can safely ignore this email.</p>
        </div>

        <!-- Indonesian Version -->
        <div class="language-section">
            <span class="language-label">Bahasa Indonesia</span>
            <p>Halo {{ $user->name }},</p>
            <p>Terima kasih telah mendaftar di Ulin Mahoni. Untuk menyelesaikan pendaftaran Anda, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </div>

            <p>Atau salin dan tempel URL berikut ke browser Anda:</p>
            <p style="word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px;">
                {{ $verificationUrl }}
            </p>

            <p>Jika Anda tidak membuat permintaan ini, Anda dapat mengabaikan email ini dengan aman.</p>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Ulin Mahoni. All rights reserved. / Hak cipta dilindungi.</p>
        <p>This is an automated email, please do not reply. / Ini adalah email otomatis, mohon untuk tidak membalas.</p>
    </div>
</body>
</html>
