@php($details = $receipt->details)
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;color:#172033;font-size:14px;line-height:1.6;">
    <tr><td align="center" style="padding:32px 24px 24px;border-top:5px solid #b6975a;border-bottom:1px solid #e9e2d5;">
        <img src="{{ $logoSrc ?? (isset($message) ? $message->embed(public_path('image/kotakia.png')) : asset('image/kotakia.png')) }}" width="64" height="64" alt="Logo Kotakia" style="display:block;width:64px;height:64px;margin:0 auto 12px;">
        <div style="font-size:22px;font-weight:700;letter-spacing:1px;"><span style="color:#b6975a;">KOTAKIA</span>.MY</div>
        <div style="font-size:12px;color:#718096;">ecard.kotakia.my &nbsp; · &nbsp; kotakiahq@gmail.com</div>
        <h1 style="font-family:Georgia,serif;font-size:28px;line-height:1.3;font-weight:400;margin:24px 0 8px;">{{ $details['paid'] ? 'Resit Bayaran' : 'Pengesahan Tempahan' }}</h1>
        <span style="display:inline-block;padding:5px 14px;border-radius:20px;background:{{ $details['paid'] ? '#eaf5ee' : '#f5f1e8' }};color:{{ $details['paid'] ? '#256340' : '#856831' }};font-size:11px;font-weight:700;letter-spacing:1px;">{{ $details['paid'] ? 'BAYARAN DITERIMA' : 'TEMPAHAN DIREKODKAN' }}</span>
    </td></tr>
    <tr><td style="padding:26px 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;table-layout:fixed;"><tr>
            <td width="52%" valign="top" style="padding-right:16px;overflow-wrap:anywhere;word-break:break-word;"><div style="font-size:10px;letter-spacing:1.5px;color:#8b774f;font-weight:700;margin-bottom:7px;">DISEDIAKAN UNTUK</div><strong>{{ $details['customer_name'] }}</strong><br><span style="font-size:12px;color:#718096;">{{ $details['customer_email'] }}</span></td>
            <td width="48%" valign="top" align="right" style="font-size:12px;"><strong>{{ $receipt->number }}</strong><br><span style="color:#718096;">{{ $details['issued_at'] }} MYT</span><br><span style="color:#718096;">Tempahan #{{ $details['order_number'] }}</span></td>
        </tr></table>
        <div style="margin-top:22px;padding:14px 16px;background:#f8fafc;border-radius:8px;overflow-wrap:anywhere;word-break:break-word;"><strong>{{ $details['title'] }}</strong><br><span style="font-size:12px;color:#718096;">Design asal: {{ $details['design'] }}</span></div>
    </td></tr>
    <tr><td style="padding:0 24px 24px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="width:100%;border-collapse:collapse;table-layout:fixed;">
            <thead><tr style="background:#172033;color:#ffffff;"><th scope="col" align="left" style="padding:12px 14px;font-size:11px;letter-spacing:1px;width:65%;">BUTIRAN / SERVIS</th><th scope="col" align="right" style="padding:12px 14px;font-size:11px;letter-spacing:1px;">JUMLAH ({{ $details['currency'] }})</th></tr></thead>
            <tbody>@foreach($details['items'] as $item)
            <tr><td style="padding:16px 14px;border-bottom:1px solid #e7ebf0;overflow-wrap:anywhere;word-break:break-word;"><strong>{{ $item['name'] }}</strong><br><span style="font-size:12px;color:#718096;">{{ $item['description'] }}</span></td><td align="right" style="padding:16px 14px;border-bottom:1px solid #e7ebf0;white-space:nowrap;">{{ number_format($item['amount_cents'] / 100, 2) }}</td></tr>
            @endforeach</tbody>
        </table>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;margin-top:20px;">
            <tr><td style="padding:6px 14px;color:#718096;">Subtotal</td><td align="right" style="padding:6px 14px;">{{ $details['currency'] }} {{ number_format($details['total_cents'] / 100, 2) }}</td></tr>
            <tr style="background:#f5f1e8;"><td style="padding:16px 14px;font-weight:700;">{{ $details['paid'] ? 'Jumlah dibayar' : 'Jumlah tempahan' }}</td><td align="right" style="padding:16px 14px;font-size:22px;font-weight:700;color:#856831;white-space:nowrap;">{{ $details['currency'] }} {{ number_format($details['total_cents'] / 100, 2) }}</td></tr>
        </table>
        <p style="font-size:11px;color:#718096;margin-top:18px;overflow-wrap:anywhere;word-break:break-all;">Rujukan transaksi<br><strong style="color:#475569;">{{ $details['reference'] }}</strong></p>
        @unless($details['paid'])<p style="font-size:12px;color:#856831;background:#fff9ea;padding:12px;border-radius:6px;">Dokumen ini mengesahkan tempahan sahaja. Tiada wang dicaj melalui sistem.</p>@endunless
    </td></tr>
    <tr><td align="center" style="padding:22px 24px;border-top:1px solid #e9e2d5;"><div style="font-family:Georgia,serif;font-size:19px;">Terima kasih kerana memilih Kotakia.</div><p style="color:#718096;font-size:12px;margin:8px 0 0;">Setiap cerita indah bermula dengan sebuah jemputan.</p><p style="color:#718096;font-size:11px;margin:14px 0 0;">Dokumen dijana oleh sistem dan tidak memerlukan tandatangan.</p></td></tr>
</table>
