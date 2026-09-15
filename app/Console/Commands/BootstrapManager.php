<?php

namespace App\Console\Commands;

use App\Http\AuditContext;
use App\Models\AccessControl;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BootstrapManager extends Command
{
    protected $signature = 'manager:bootstrap {email : Emel akaun sedia ada untuk manager pertama}';

    protected $description = 'Tetapkan manager pertama sahaja; akaun manager tambahan mesti dicipta melalui portal manager.';

    public function handle(): int
    {
        try {
            DB::transaction(function (): void {
                AccessControl::where('key', 'login')->lockForUpdate()->firstOrFail();
                if (User::where('role', 'manager')->exists()) {
                    throw new RuntimeException('Manager sudah wujud. Gunakan portal manager untuk menambah akaun.');
                }
                $user = User::where('email', strtolower(trim($this->argument('email'))))->lockForUpdate()->first();
                if (! $user || ! $user->is_active) {
                    throw new RuntimeException('Akaun aktif dengan emel tersebut tidak ditemui.');
                }
                $before = AuditContext::snapshot($user);
                $user->forceFill(['role' => 'manager'])->save();
                AuditLog::create([
                    'user_id' => $user->id, 'action' => 'manager.bootstrapped', 'outcome' => 'success',
                    'method' => 'CLI', 'route_name' => 'manager:bootstrap', 'status_code' => 200,
                    'subject_type' => 'user', 'subject_id' => $user->id,
                    'metadata' => ['before' => $before, 'after' => AuditContext::snapshot($user)],
                ]);
            });
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $this->info('Manager pertama telah diaktifkan. Log masuk menggunakan kata laluan akaun sedia ada.');

        return self::SUCCESS;
    }
}
