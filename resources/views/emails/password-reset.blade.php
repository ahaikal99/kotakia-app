<!DOCTYPE html>
<html lang="ms"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#f5f6f8;font-family:Arial,sans-serif;color:#142039;padding:30px 15px">
<table role="presentation" style="width:100%;max-width:560px;margin:auto;background:white;border-top:4px solid #b6975a" cellspacing="0" cellpadding="0"><tr><td style="padding:36px 28px">
    @include('emails.brand')
    <h1 style="font-family:Georgia,serif;font-size:28px;text-align:center;margin:32px 0">Tetapkan semula kata laluan</h1>
    <p style="line-height:1.8;color:#647084">Kami menerima permintaan untuk menukar kata laluan akaun Kotakia anda. Klik butang di bawah untuk menetapkan kata laluan baharu.</p>
    <p style="text-align:center;margin:30px 0"><a href="{{ $url }}" style="display:inline-block;padding:16px 24px;background:#142039;color:white;text-decoration:none;border-radius:8px;font-weight:bold">Reset kata laluan</a></p>
    <p style="font-size:13px;line-height:1.8;color:#647084">Pautan ini sah selama {{ $minutes }} minit dan hanya boleh digunakan sekali. Jika anda tidak membuat permintaan ini, abaikan email ini. Kata laluan anda kekal seperti biasa.</p>
    <p style="font-size:12px;line-height:1.8;color:#647084">Butang tidak berfungsi? Salin pautan ini ke pelayar:<br><a href="{{ $url }}" style="word-break:break-all;color:#92743e">{{ $url }}</a></p>
    <p style="font-size:12px;line-height:1.8;color:#647084;text-align:center;border-top:1px solid #eee4ce;padding-top:20px;">Perlukan bantuan? <a href="mailto:kotakiahq@gmail.com" style="color:#92743e;">kotakiahq@gmail.com</a></p>
</td></tr></table></body></html>
