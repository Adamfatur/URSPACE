<!DOCTYPE html>
<html>

<head>
    <title>{{ $subjectLine }}</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4CAF50; margin: 0 0 12px;">Forum Universitas Raharja</h2>
        <p style="margin: 0 0 12px;"><strong>{{ $subjectLine }}</strong></p>
        <p style="margin: 0 0 16px;">{{ $messageLine }}</p>

        @if (!empty($actionUrl) && !empty($actionText))
            <div style="margin: 20px 0;">
                <a href="{{ $actionUrl }}" style="display: inline-block; background-color: #4CAF50; color: #fff; text-decoration: none; padding: 10px 14px; border-radius: 6px;">
                    {{ $actionText }}
                </a>
            </div>
            <p style="margin: 0; font-size: 12px; color: #666;">Jika tombol tidak berfungsi, buka link ini: {{ $actionUrl }}</p>
        @endif

        <br>
        <p style="margin: 0;">Terima kasih,<br>Tim Forum UR</p>
    </div>
</body>

</html>
