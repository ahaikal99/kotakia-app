Salam {{ $receipt->details['customer_name'] }},

{{ $receipt->details['paid'] ? 'Bayaran anda telah diterima.' : 'Tempahan anda telah direkodkan. Tiada wang dicaj melalui sistem.' }}

Dokumen: {{ $receipt->number }}
Tarikh: {{ $receipt->details['issued_at'] }} MYT
Tempahan: {{ $receipt->details['order_number'] }}
@foreach($receipt->details['items'] as $item)
{{ $item['name'] }} — {{ $receipt->details['currency'] }} {{ number_format($item['amount_cents'] / 100, 2) }} ({{ $item['description'] }})
@endforeach
Jumlah: {{ $receipt->details['currency'] }} {{ number_format($receipt->details['total_cents'] / 100, 2) }}
Rujukan: {{ $receipt->details['reference'] }}

Lihat dan cetak resit (log masuk diperlukan):
{{ rtrim(config('app.url'), '/').route('receipts.show', $receipt->payment->invitation_id, false) }}

Terima kasih kerana memilih Kotakia.
kotakiahq@gmail.com
