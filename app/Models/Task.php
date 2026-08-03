<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
    'created_by',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'deadline' => 'datetime',
        ];
    }
}
