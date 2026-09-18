<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Models\Design;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DesignController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'theme' => ['nullable', Rule::in(array_keys(config('catalog.themes')))],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);
        $designs = Design::query()
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('code', 'like', '%'.$search.'%')))
            ->when($filters['theme'] ?? null, fn ($query, $theme) => $query->where('theme', $theme))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->orderBy('code')->paginate(12)->withQueryString();

        return view('manager.designs.index', ['designs' => $designs, 'themes' => config('catalog.themes')]);
    }

    public function create(): View
    {
        return view('manager.designs.create', ['themes' => config('catalog.themes')]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => strtoupper(trim($request->input('code')))]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z][A-Z0-9_-]*$/', 'unique:designs,code'],
            'theme' => ['required', Rule::in(array_keys(config('catalog.themes')))],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Sila masukkan nama design.',
            'code.required' => 'Sila masukkan kod design.',
            'code.regex' => 'Kod mesti bermula dengan huruf dan hanya mengandungi huruf, nombor, sengkang atau garis bawah.',
            'code.unique' => 'Kod design ini telah digunakan.',
            'theme.required' => 'Sila pilih kategori.',
            'theme.in' => 'Kategori tidak sah.',
        ]);
        $design = Design::create($data);
        AuditContext::mark($request, 'design.created', $design);

        return to_route('manager.designs')->with('status', 'Design berjaya ditambah.');
    }

    public function status(Request $request, Design $design): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $before = AuditContext::snapshot($design);
        $design->update($data);
        AuditContext::mark($request, $design->is_active ? 'design.activated' : 'design.deactivated', $design, $before);

        return back()->with('status', $design->is_active ? 'Design telah diaktifkan.' : 'Design telah dinyahaktifkan. Tempahan sedia ada dikekalkan.');
    }
}
