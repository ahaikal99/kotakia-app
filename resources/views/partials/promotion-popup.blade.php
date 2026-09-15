@if($promotion ?? null)
<div data-promotion-container>
<dialog class="promotion-dialog" data-promotion-dialog data-expires-at="{{ $promotion->expires_at->toIso8601String() }}" aria-labelledby="promotion-title">
<div class="flex items-center justify-between gap-4 px-5 pt-5 pb-3"><span id="promotion-title" class="eyebrow">Promosi Kotakia</span><button type="button" data-promotion-close autofocus aria-label="Tutup promosi" class="rounded-full bg-slate-100 p-2.5 hover:bg-slate-200"><x-icon name="close"/></button></div>
<img src="{{ route('promotions.poster',$promotion->id) }}" alt="{{ $promotion->title }}" class="promotion-poster">
</dialog>
</div>
@endif

