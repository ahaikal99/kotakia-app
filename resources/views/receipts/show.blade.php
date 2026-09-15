<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $receipt->number }} — Kotakia</title>
    <style>
        * { box-sizing:border-box; } body { margin:0;padding:28px 16px;background:#eef1f5;font-family:Arial,Helvetica,sans-serif;color:#172033; }
        .toolbar { max-width:740px;margin:0 auto 20px;display:flex;flex-wrap:wrap;align-items:center;gap:10px; }
        .button { display:inline-flex;align-items:center;gap:8px;padding:12px 16px;border-radius:8px;border:1px solid #cbd5e1;background:white;color:#172033;font:600 13px Arial;text-decoration:none;cursor:pointer; }
        .primary { background:#172033;color:white;border-color:#172033; } .sheet { max-width:740px;margin:auto;background:white;box-shadow:0 8px 36px #17203312; }
        .status { max-width:740px;margin:0 auto 18px;color:#64748b;font-size:13px;line-height:1.6;overflow-wrap:anywhere; }
        @page { size:A4;margin:14mm; } @media print { body { background:white;padding:0; } .toolbar,.status { display:none; } .sheet { box-shadow:none;max-width:none; } tr { break-inside:avoid; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <a class="button" href="{{ route('payments.show', $receipt->payment->invitation_id) }}">← Kembali</a>
        <button class="button primary" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        @if(in_array($receipt->email_status, ['not_sent', 'failed'], true))
        <form method="POST" action="{{ route('receipts.send', $receipt->payment->invitation_id) }}">@csrf<button class="button" type="submit">{{ $receipt->email_status === 'failed' ? 'Cuba Hantar Email Semula' : 'Hantar ke Email' }}</button></form>
        @endif
    </div>
    <div class="status">{{ $receipt->emailStatusLabel() }} · {{ $receipt->details['customer_email'] }}
        @if($receipt->emailed_at)
            · {{ $receipt->emailed_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }} MYT
        @endif
        <br>Gunakan pilihan “Save as PDF” dalam menu cetakan untuk menyimpan salinan.
    </div>
    <main class="sheet">@include('receipts.document')</main>
</body>
</html>
