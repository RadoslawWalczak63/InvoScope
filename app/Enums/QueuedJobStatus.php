<?php

namespace App\Enums;

enum QueuedJobStatus: string
{
    case NEW = 'New';
    case QUEUED = 'Queued';
    case PROCESSING = 'Processing';
    case FINISHED = 'Finished';
    case FAILED = 'Failed';
}
