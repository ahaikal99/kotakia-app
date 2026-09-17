<!DOCTYPE html>
<html lang="ms"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Selamat datang ke Kotakia</title></head>
<body style="margin:0;background:#eef1f5;font-family:Arial,Helvetica,sans-serif;color:#142039;">
<div style="display:none;max-height:0;overflow:hidden;">Akaun anda sedia digunakan. Mulakan jemputan pertama anda bersama Kotakia.</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center" style="padding:28px 12px;">
<table role="presentation" width="560" style="width:100%;max-width:560px;background:white;border-top:5px solid #b6975a;" cellspacing="0" cellpadding="0"><tr><td style="padding:36px 28px;">
    @include('emails.brand')
    <h1 style="font-family:Georgia,serif;font-size:30px;font-weight:400;text-align:center;margin:28px 0;">Selamat datang ke Kotakia.</h1>
    <p style="line-height:1.8;">Salam {{ $customerName }},</p>
    <p style="line-height:1.8;color:#647084;">Terima kasih kerana mendaftar. Akaun anda telah berjaya dicipta dan sedia digunakan untuk mengurus jemputan hari istimewa anda.</p>
    <table role="presentation" width="100%" style="background:#faf7ef;border:1px solid #eee4ce;margin:24px 0;" cellpadding="18" cellspacing="0"><tr><td style="font-size:14px;line-height:2;color:#647084;"><strong style="color:#142039;">Jemputan pertama anda bermula di sini</strong><br>01 &nbsp; Log masuk ke akaun anda.<br>02 &nbsp; Klik Cipta Kad dan isi maklumat majlis.<br>03 &nbsp; Pilih pakej serta design kegemaran anda.</td></tr></table>
    <p style="text-align:center;margin:28px 0;"><a href="{{ rtrim(config('app.url'), '/').route('login', [], false) }}" style="display:inline-block;background:#142039;color:white;text-decoration:none;border-radius:8px;padding:16px 28px;font-weight:bold;">Log masuk ke Kotakia</a></p>
    <p style="font-size:12px;line-height:1.8;color:#647084;text-align:center;">Perlukan bantuan? Balas email ini atau hubungi<br><a href="mailto:kotakiahq@gmail.com" style="color:#92743e;">kotakiahq@gmail.com</a></p>
</td></tr></table></td></tr></table></body></html>
