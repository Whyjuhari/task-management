<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'description',
    'instructions',
    'category',
    'start_date',
    'deadline',
    'submission_type',
    'status',
])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const SUBMISSION_TYPE_FILE = 'file';

    public const SUBMISSION_TYPE_LINK = 'link';

    public const SUBMISSION_TYPE_FILE_OR_LINK = 'file_or_link';

    public const SUBMISSION_TYPES = [
        self::SUBMISSION_TYPE_FILE,
        self::SUBMISSION_TYPE_LINK,
        self::SUBMISSION_TYPE_FILE_OR_LINK,
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [self::STATUS_DRAFT, self::STATUS_ACTIVE, self::STATUS_CLOSED];

    public const PARTICIPANT_VISIBLE_STATUSES = [self::STATUS_ACTIVE, self::STATUS_CLOSED];

    public const PERSONAL_STATUS_NOT_SUBMITTED = 'not_submitted';

    public const PERSONAL_STATUS_SUBMITTED = 'submitted';

    public const PERSONAL_STATUS_LATE = 'late';

    public const PERSONAL_STATUS_DEADLINE_ENDED = 'deadline_ended';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function scopeVisibleToParticipants(Builder $query): Builder
    {
        return $query->whereIn('status', self::PARTICIPANT_VISIBLE_STATUSES);
    }

    public function submissionFor(User $user): ?Submission
    {
        if ($this->relationLoaded('submissions')) {
            return $this->submissions->firstWhere('user_id', $user->getKey());
        }

        return $this->submissions()
            ->where('user_id', $user->getKey())
            ->first();
    }

    public function personalStatusFor(User $user): string
    {
        $submission = $this->submissionFor($user);

        if ($submission !== null) {
            if (
                $submission->status === Submission::STATUS_LATE
                || $submission->submitted_at->isAfter($this->deadline)
            ) {
                return self::PERSONAL_STATUS_LATE;
            }

            return self::PERSONAL_STATUS_SUBMITTED;
        }

        if ($this->status === self::STATUS_CLOSED || $this->deadline->isPast()) {
            return self::PERSONAL_STATUS_DEADLINE_ENDED;
        }

        return self::PERSONAL_STATUS_NOT_SUBMITTED;
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && ($this->start_date === null || $this->start_date->lessThanOrEqualTo(now()));
    }

    public function remainingTime(): string
    {
        if ($this->status === self::STATUS_CLOSED) {
            return 'Tugas telah ditutup';
        }

        $deadline = $this->deadline->copy()->locale('id');
        $difference = $deadline->diffForHumans(now(), CarbonInterface::DIFF_ABSOLUTE, false, 2);

        return $deadline->isFuture()
            ? "{$difference} lagi"
            : "Berakhir {$difference} lalu";
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'deadline' => 'datetime',
        ];
    }
}
