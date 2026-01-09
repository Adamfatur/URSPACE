<!DOCTYPE html>
<html>

<head>
    <title>Kode Autentikasi 2FA</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4CAF50;">Login Verification</h2>
        <p>Halo {{ $user->name }},</p>
        <p>Anda mencoba masuk ke Forum Universitas Raharja. Gunakan kode berikut untuk menyelesaikan proses login:</p>

        <div style="background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;">
            <h1 style="margin: 0; letter-spacing: 5px; color: #333;">{{ $code }}</h1>
        </div>

        <p>Kode ini akan kadaluarsa dalam 10 menit.</p>
        <p>Jika Anda tidak merasa melakukan login ini, abaikan email ini.</p>
        <br>
        <p>Terima kasih,<br>Tim IT Universitas Raharja</p>
    </div>
</body>

</html>