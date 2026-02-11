<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\DataChanged;

class TaskObserver
{
    public function created(Task $task)
    {
        event(new DataChanged($task));
    }

    public function updated(Task $task)
    {
        event(new DataChanged($task));
    }

    public function deleted(Task $task)
    {
        event(new DataChanged($task->id));
    }
}
