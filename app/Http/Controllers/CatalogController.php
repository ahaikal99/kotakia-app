<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $themes = config('catalog.themes');
        $theme = $request->query('tema', 'semua');
        $sort = $request->query('susun', 'kod');

        if (! is_string($theme) || ! array_key_exists($theme, $themes)) {
            $theme = 'semua';
        }

        if (! in_array($sort, ['kod', 'nama-az', 'nama-za'], true)) {
            $sort = 'kod';
        }

        $collection = collect(config('catalog.designs'));
        $counts = $collection->countBy('theme');
        $designs = $collection->when($theme !== 'semua', fn ($items) => $items->where('theme', $theme));
        $designs = match ($sort) {
            'nama-az' => $designs->sortBy('name'),
            'nama-za' => $designs->sortByDesc('name'),
            default => $designs->sortBy('code'),
        };

        return view('catalog', compact('themes', 'theme', 'sort', 'designs', 'counts'))
            ->with('total', $collection->count());
    }
}
