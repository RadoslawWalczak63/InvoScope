<?php

namespace App\Http\Resources;

use App\Models\QueuedJob;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QueuedJob
 *
 * @property-read Carbon $started_at
 * @property-read Carbon $finished_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class QueuedJobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'job' => $this->job,
            'job_display_name' => $this->job_display_name,
            'arguments' => $this->arguments,
            'result' => $this->result,
            'status' => $this->status,
            'started_at' => $this->started_at?->toDateTimeString(),
            'finished_at' => $this->finished_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'console_output' => $this->console_output,
        ];
    }
}
