<?php

namespace App\Models;

use App\Enums\QueuedJobStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property object $arguments
 * @property object $result
 * @property string $job
 * @property QueuedJobStatus $status
 * @property Carbon $started_at
 * @property Carbon $finished_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $console_output
 * @property string $job_display_name
 */
class QueuedJob extends Model
{
    protected $appends = [
        'job_display_name',
    ];

    protected $attributes = [
        'status' => QueuedJobStatus::NEW,
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'arguments' => 'object',
            'result' => 'object',
            'status' => QueuedJobStatus::class,
            'started_at' => 'datetime:Y-m-d H:i:s',
            'finished_at' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function jobDisplayName(): Attribute
    {
        return Attribute::get(
            fn () => Str::headline(class_basename($this->job))
        );
    }

    public function dispatch(): void
    {
        $this->update(['status' => QueuedJobStatus::QUEUED]);

        Artisan::queue($this->job, [
            '--queuedJobId' => $this->id,
        ]);
    }

    public function started(): void
    {
        $this->update([
            'status' => QueuedJobStatus::PROCESSING,
            'started_at' => now(),
            'console_output' => '',
        ]);
    }

    public function finished(array|object $result = []): void
    {
        $this->update([
            'status' => QueuedJobStatus::FINISHED,
            'result' => $result,
            'finished_at' => now(),
        ]);
    }

    public function failed(array|object $result = []): void
    {
        $this->update([
            'status' => QueuedJobStatus::FAILED,
            'result' => $result,
            'finished_at' => now(),
        ]);
    }

    public function addLog(string $log): void
    {
        $this->console_output .= sprintf(
            "%s: %s\n",
            now()->format('Y-m-d H:i:s'),
            $log,
        );

        $this->save();
    }
}
