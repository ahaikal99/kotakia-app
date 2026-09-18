<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignPreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $code): View
    {
        $design = Design::where('code', strtoupper($code))->firstOrFail();
        abort_unless($design->is_active || $request->user()?->isManager(), 404);
        abort_unless($design->hasTemplate(), 404);

        return view($design->templateView(), compact('design'));
    }
}
