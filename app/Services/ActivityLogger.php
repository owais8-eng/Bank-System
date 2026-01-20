<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity_log;

class ActivityLogger
{
    public static function log(
        string $description,
        ?object $subject = null,
        ?object $causer = null,
        ?array $properties = null,
        ?string $logName = null
    ): void {
        Activity_log::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject->id ?? null,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer->id ?? null,
            'properties' => $properties,
        ]);
    }
}
