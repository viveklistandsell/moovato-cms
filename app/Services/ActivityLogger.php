<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Single entry point for recording activity log rows. Resolves the actor +
 * request fingerprint (IP, user agent) from the current request automatically
 * so callers stay terse:
 *
 *   app(ActivityLogger::class)->log(
 *       'user.created',
 *       'Created user {actor} → {subject}',
 *       $newUser,
 *       ['role' => 'Editor'],
 *   );
 */
final class ActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $properties
     */
    public function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        ?array $properties = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 1000),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
        ]);
    }

    /**
     * Convenience for the common create / update / delete trio so call sites
     * don't have to remember the action naming convention.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public function created(Model $subject, ?string $description = null, ?array $properties = null): ActivityLog
    {
        return $this->log($this->actionFor($subject, 'created'), $description, $subject, $properties);
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    public function updated(Model $subject, ?string $description = null, ?array $properties = null): ActivityLog
    {
        return $this->log($this->actionFor($subject, 'updated'), $description, $subject, $properties);
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    public function deleted(Model $subject, ?string $description = null, ?array $properties = null): ActivityLog
    {
        return $this->log($this->actionFor($subject, 'deleted'), $description, $subject, $properties);
    }

    /** Build a `subject.verb` action key from the model + verb (e.g. user.created). */
    private function actionFor(Model $subject, string $verb): string
    {
        $class = class_basename($subject);

        return mb_strtolower($class).'.'.$verb;
    }
}
