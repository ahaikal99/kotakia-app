<!DOCTYPE html>
<html lang="ms"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $receipt->number }} — Kotakia</title></head>
<body style="margin:0;padding:0;background:#eef1f5;font-family:Arial,Helvetica,sans-serif;">
    <div style="display:none;max-height:0;overflow:hidden;">{{ $receipt->details['paid'] ? 'Bayaran anda telah diterima.' : 'Tempahan anda telah direkodkan.' }} Butiran {{ $receipt->number }} tersedia di dalam email ini.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f5;width:100%;"><tr><td align="center" style="padding:28px 12px;">
        <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="width:100%;max-width:640px;">
            <tr><td style="padding:0 12px 22px;color:#475569;font-size:14px;line-height:1.7;">Salam {{ $receipt->details['customer_name'] }},<br>{{ $receipt->details['paid'] ? 'Terima kasih, bayaran anda telah diterima. Berikut ialah resit untuk simpanan anda.' : 'Terima kasih. Berikut ialah pengesahan tempahan untuk simpanan anda.' }}</td></tr>
            <tr><td style="background:white;">@include('receipts.document')</td></tr>
            <tr><td align="center" style="padding:28px 12px;">
                <a href="{{ rtrim(config('app.url'), '/').route('receipts.show', $receipt->payment->invitation_id, false) }}" style="display:inline-block;background:#172033;color:#ffffff;text-decoration:none;font-size:14px;font-weight:bold;border-radius:8px;padding:15px 24px;">Lihat & Cetak Resit</a>
                <p style="font-size:12px;color:#718096;line-height:1.7;">Log masuk ke akaun Kotakia anda untuk melihat resit.<br>Perlukan bantuan? Balas email ini atau hubungi <a href="mailto:kotakiahq@gmail.com" style="color:#856831;">kotakiahq@gmail.com</a>.</p>
            </td></tr>
        </table>
    </td></tr></table>
</body></html>
