<?php

namespace App\Models;

use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'submission_link',
    'note',
])]
class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_LATE = 'late';

    public const STATUSES = [self::STATUS_SUBMITTED, self::STATUS_LATE];

    public static function hasValidPrivateFilePath(mixed $path): bool
    {
        return is_string($path)
            && preg_match('/\Asubmissions\/[A-Za-z0-9._-]+\z/D', $path) === 1;
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }
}
