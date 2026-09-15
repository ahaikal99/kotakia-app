<?php

namespace App\Http;

use App\Models\AccessControl;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\PromotionBanner;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditContext
{
    public static function snapshot(Model $model): array
    {
        $fields = match (true) {
            $model instanceof Invitation => [...$model->getFillable(), 'package_code', 'package_name', 'design_code', 'design_name', 'amount_cents', 'status'],
            $model instanceof Payment => ['invitation_id', 'reference', 'amount_cents', 'currency', 'status', 'method'],
            $model instanceof User => ['name', 'email', 'phone', 'role', 'is_active', 'blocked_reason'],
            $model instanceof AccessControl => ['key', 'is_blocked', 'reason', 'updated_by'],
            $model instanceof PromotionBanner => ['title', 'image_path', 'is_active', 'expires_at', 'created_by', 'updated_by'],
            default => [],
        };

        return array_intersect_key($model->getAttributes(), array_flip($fields));
    }

    public static function mark(Request $request, string $action, ?Model $subject = null, ?array $before = null): void
    {
        $context = ['action' => $action];
        if ($subject !== null) {
            $after = self::snapshot($subject);
            $context['subject_type'] = strtolower(class_basename($subject));
            $context['subject_id'] = $subject->getKey();
            $context['after'] = $after;
            if ($before !== null) {
                $keys = array_keys(array_filter($after, fn ($value, $key) => ($before[$key] ?? null) !== $value, ARRAY_FILTER_USE_BOTH));
                $context['before'] = array_intersect_key($before, array_flip($keys));
                $context['after'] = array_intersect_key($after, array_flip($keys));
            }
        }
        $request->attributes->set('audit.context', $context);
        app('request')->attributes->set('audit.context', $context);
    }
}
