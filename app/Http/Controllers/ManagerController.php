<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Http\Requests\RegisterRequest;
use App\Models\AccessControl;
use App\Models\AuditLog;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function index(): View
    {
        return view('manager.index', [
            'customers' => User::where('role', 'customer')->count(),
            'managers' => User::where('role', 'manager')->where('is_active', true)->count(),
            'orders' => Invitation::count(),
            'pending' => Invitation::where('status', 'awaiting_payment')->count(),
            'simulated' => Payment::where('status', 'simulated')->sum('amount_cents'),
            'blocked' => AccessControl::where('is_blocked', true)->get(),
            'recentOrders' => Invitation::with('user')->latest()->limit(5)->get(),
        ]);
    }

    public function orders(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['draft', 'awaiting_payment', 'demo_complete'])]]);
        $orders = Invitation::with(['user', 'payment'])
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('title', 'like', '%'.$q.'%')->orWhere('id', ctype_digit($q) ? $q : 0)->orWhereHas('user', fn ($query) => $query->where('email', 'like', '%'.$q.'%')->orWhere('name', 'like', '%'.$q.'%'))))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()->paginate(15)->withQueryString();

        return view('manager.orders', compact('orders'));
    }

    public function order(int $order): View
    {
        return view('manager.order', ['order' => Invitation::with(['user', 'payment'])->findOrFail($order)]);
    }

    public function users(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'role' => ['nullable', Rule::in(['customer', 'manager'])], 'active' => ['nullable', Rule::in(['0', '1'])]]);
        $users = User::withCount('invitations')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%')->orWhere('phone', 'like', '%'.$q.'%')))
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(isset($filters['active']), fn ($query) => $query->where('is_active', $filters['active']))
            ->latest()->paginate(15)->withQueryString();

        return view('manager.users', compact('users'));
    }

    public function user(int $user): View
    {
        $account = User::findOrFail($user);

        return view('manager.user', ['account' => $account, 'orders' => $account->invitations()->latest()->paginate(10)]);
    }

    public function createManager(): View
    {
        return view('manager.create-manager');
    }

    public function storeManager(RegisterRequest $request): RedirectResponse
    {
        $user = new User($request->validated());
        $user->forceFill(['role' => 'manager', 'is_active' => true])->save();
        AuditContext::mark($request, 'manager.account_created', $user);

        return to_route('manager.users.show', $user->id)->with('status', 'Akaun manager berjaya dicipta. Manager boleh log masuk melalui halaman login sedia ada.');
    }

    public function updateUser(Request $request, int $user): RedirectResponse
    {
        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
            'blocked_reason' => ['nullable', 'required_if:is_active,0', 'string', 'max:500'],
        ], ['blocked_reason.required_if' => 'Sila masukkan sebab sekatan akaun.']);
        DB::transaction(function () use ($request, $user, $data): void {
            $managers = User::where('role', 'manager')->orderBy('id')->lockForUpdate()->get();
            $account = User::lockForUpdate()->findOrFail($user);
            if (! $data['is_active'] && ($account->id === $request->user()->id || ($account->isManager() && $managers->where('is_active', true)->count() <= 1))) {
                throw ValidationException::withMessages(['is_active' => 'Anda tidak boleh menyekat akaun sendiri atau manager aktif terakhir.']);
            }
            $before = AuditContext::snapshot($account);
            $account->forceFill(['is_active' => (bool) $data['is_active'], 'blocked_reason' => $data['is_active'] ? null : $data['blocked_reason']])->save();
            AuditContext::mark($request, $data['is_active'] ? 'manager.account_unblocked' : 'manager.account_blocked', $account, $before);
        });

        return back()->with('status', 'Status akaun telah dikemas kini.');
    }

    public function logs(Request $request): View
    {
        $filters = $request->validate([
            'action' => ['nullable', 'string', 'max:100'], 'ip' => ['nullable', 'ip'],
            'user_id' => ['nullable', 'integer', 'min:1'], 'outcome' => ['nullable', Rule::in(['success', 'failed', 'redirected'])],
            'from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        $logs = AuditLog::with('user')
            ->when($filters['action'] ?? null, fn ($q, $v) => $q->where('action', $v))
            ->when($filters['ip'] ?? null, fn ($q, $v) => $q->where('ip_address', $v))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['outcome'] ?? null, fn ($q, $v) => $q->where('outcome', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->where('created_at', '>=', Carbon::parse($v, 'Asia/Kuala_Lumpur')->startOfDay()->utc()))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->where('created_at', '<=', Carbon::parse($v, 'Asia/Kuala_Lumpur')->endOfDay()->utc()))
            ->latest('id')->paginate(25)->withQueryString();

        return view('manager.logs', ['logs' => $logs, 'actions' => AuditLog::distinct()->orderBy('action')->pluck('action')]);
    }

    public function log(int $log): View
    {
        return view('manager.log', ['log' => AuditLog::with('user')->findOrFail($log)]);
    }

    public function controls(): View
    {
        return view('manager.controls', ['controls' => AccessControl::with('updater')->get()]);
    }

    public function updateControl(Request $request, string $key): RedirectResponse
    {
        abort_unless(array_key_exists($key, AccessControl::LABELS), 404);
        $data = $request->validate([
            'is_blocked' => ['required', 'boolean'],
            'reason' => ['nullable', 'required_if:is_blocked,1', 'string', 'max:500'],
            'version' => ['required', 'string'],
        ], ['reason.required_if' => 'Sila masukkan sebab sebelum menyekat halaman.']);
        DB::transaction(function () use ($request, $key, $data): void {
            $control = AccessControl::where('key', $key)->lockForUpdate()->firstOrFail();
            if ($data['version'] !== hash('sha256', $control->getRawOriginal('updated_at').json_encode([$control->is_blocked, $control->reason, $control->updated_by]))) {
                throw ValidationException::withMessages(['version' => 'Tetapan telah berubah. Muat semula halaman sebelum menyimpan.']);
            }
            $before = AuditContext::snapshot($control);
            $control->update(['is_blocked' => (bool) $data['is_blocked'], 'reason' => $data['is_blocked'] ? $data['reason'] : null, 'updated_by' => $request->user()->id]);
            AuditContext::mark($request, $data['is_blocked'] ? 'manager.page_blocked' : 'manager.page_unblocked', $control, $before);
        });

        return to_route('manager.controls')->with('status', 'Kawalan akses telah dikemas kini dan berkuat kuasa serta-merta.');
    }
}
