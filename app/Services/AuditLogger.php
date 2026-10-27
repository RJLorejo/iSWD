<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuditLogger
{
    /**
     * Record a significant system activity.
     *
     * Do not pass passwords, tokens, or unnecessary personal data
     * in the old_values/new_values arguments.
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            $user = Auth::user();

            $role = null;

            if ($user && method_exists($user, 'getRoleNames')) {
                $role = $user->getRoleNames()->first();
            }

            $request = request();

            AuditLog::create([
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->full_name
                    ?? $user?->name
                    ?? trim(
                        ($user?->first_name ?? '') . ' ' .
                        ($user?->last_name ?? '')
                    )
                    ?: ($user ? 'User #' . $user->getAuthIdentifier() : 'Guest'),

                'user_role' => $role,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject
                    ? class_basename($subject)
                    : null,
                'subject_id' => $subject?->getKey(),
                'old_values' => self::sanitize($oldValues),
                'new_values' => self::sanitize($newValues),
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'http_method' => $request?->method(),
                'url' => $request
                    ? mb_substr($request->fullUrl(), 0, 2000)
                    : null,
            ]);
        } catch (Throwable $exception) {
            // Do not silently hide audit failures from the application logs.
            // This avoids replacing an existing business operation's response
            // with an unrelated audit-logging exception.
            Log::error('Failed to write system audit log.', [
                'action' => $action,
                'module' => $module,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private static function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'remember_token',
            'api_token',
            'secret',
            'otp',
            'verification_code',
        ];

        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                unset($values[$key]);
            }
        }

        return $values;
    }
}