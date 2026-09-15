<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Http\Requests\PromotionBannerRequest;
use App\Models\PromotionBanner;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PromotionBannerController extends Controller
{
    public function index(): View
    {
        return view('manager.banners.index', ['banners' => PromotionBanner::latest('id')->paginate(12)]);
    }

    public function create(): View
    {
        return view('manager.banners.form', ['banner' => new PromotionBanner]);
    }

    public function edit(int $banner): View
    {
        return view('manager.banners.form', ['banner' => PromotionBanner::findOrFail($banner)]);
    }

    public function store(PromotionBannerRequest $request): RedirectResponse
    {
        return $this->save($request, new PromotionBanner);
    }

    public function update(PromotionBannerRequest $request, int $banner): RedirectResponse
    {
        return $this->save($request, PromotionBanner::findOrFail($banner));
    }

    private function save(PromotionBannerRequest $request, PromotionBanner $banner): RedirectResponse
    {
        $data = $request->validated();
        $before = $banner->exists ? AuditContext::snapshot($banner) : null;
        $oldPath = $banner->image_path;
        $path = $request->hasFile('poster') ? $request->file('poster')->store('promotion-banners', 'local') : null;
        abort_if($request->hasFile('poster') && ! $path, 500, 'Poster tidak dapat disimpan. Sila cuba semula.');
        try {
            $banner->fill([
                'title' => $data['title'],
                'image_path' => $path ?: $oldPath,
                'is_active' => (bool) $data['is_active'],
                'expires_at' => Carbon::createFromFormat('!Y-m-d', $data['expires_on'], 'Asia/Kuala_Lumpur')->addDay()->utc(),
                'created_by' => $banner->created_by ?? $request->user()->id,
                'updated_by' => $request->user()->id,
            ])->save();
        } catch (Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }
        if ($path && $oldPath) {
            Storage::disk('local')->delete($oldPath);
        }
        AuditContext::mark($request, $before === null ? 'manager.banner_created' : 'manager.banner_updated', $banner, $before);

        return to_route('manager.banners')->with('status', 'Banner promosi telah disimpan.');
    }

    public function deactivate(Request $request, int $banner): RedirectResponse
    {
        $banner = PromotionBanner::findOrFail($banner);
        $before = AuditContext::snapshot($banner);
        $banner->update(['is_active' => false, 'updated_by' => $request->user()->id]);
        AuditContext::mark($request, 'manager.banner_deactivated', $banner, $before);

        return to_route('manager.banners')->with('status', 'Banner telah dinyahaktifkan.');
    }

    public function poster(Request $request, int $banner): StreamedResponse
    {
        $query = PromotionBanner::query();
        if (! $request->user()->isManager()) {
            $query->available();
        }
        $banner = $query->findOrFail($banner);
        abort_unless(Storage::disk('local')->exists($banner->image_path), 404);

        return Storage::disk('local')->response($banner->image_path, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
