<?php

namespace App\Services\Tasks;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\RoleResource;
use App\Models\Park;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Notifications\Task\TaskDoneNotification;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskService
{
    public function props(): array
    {
        return [
            'roles' => RoleResource::collection($this->user()->allRoles()),
            'parks' => Park::query()->get(),
            'views' => mapForSelect(Task::VIEWS),
            'priorities' => mapForSelect(Task::PRIORITIES),
            'statuses' => mapForSelect(Task::STATUSES),
            'task_types' => mapForSelect(Task::TASK_TYPES),
        ];
    }

    public function save(Task $task, TaskRequest $request): Task
    {
        $task->fill([
            'responsible_id' => $request['responsible_id'],
            'role_id' => $request['role_id'],
            'park_id' => $request['park_id'],
            'task_type' => $request['task_type'],
            'status' => $request['status'],
            'priority' => $request['priority'],
            'name' => $request['name'],
            'description' => $request['description'],
            'deadline' => $request['deadline'],
            'travel_time' => $request['travel_time'],
            'estimated_hour' => $request['estimated_hour'],
            'date' => $request['date'],
        ]);

        if ($task->isDirty('status')) {
            $task->status_changed_at = now();
        }

        $task->save();

        $this->syncSubtasks($task, $request['subtasks'] ?? []);
        $this->syncTags($task, $request['tags'] ?? []);
        $this->syncFiles($task, $request['files'] ?? []);

        return $task;
    }

    protected function syncSubtasks(Task $task, array $subtasks): Task
    {
        $task->loadMissing('subtasks');

        // not story anymore and has subtasks
        if ($task->task_type != 'story' && $task->subtasks) {
            Task::query()
                ->whereIn('id', $task->subtasks->pluck('id')->toArray())
                ->update([
                    'parent_id' => null,
                ]);

            return $task;
        }

        $tasks = mapFromSelect($subtasks, 'id');

        // remove subtasks
        Task::query()
            ->whereIn('id', $task->subtasks->pluck('id')->toArray())
            ->whereNotIn('id', $tasks)
            ->update([
                'parent_id' => null,
            ]);

        // attach new subtasks
        Task::query()
            ->whereIn('id', $tasks)
            ->update([
                'parent_id' => $task->id,
            ]);

        return $task;
    }

    protected function syncFiles(Task $task, array $files): void
    {
        $task->loadMissing('media');

        // move new/uploaded files
        collect($files)
            ->filter(function ($file) {
                return isset($file['path']);
            })
            ->each(function ($file) use ($task) {
                $task
                    ->addMedia(storage_path('app/'.$file['path']))
                    ->usingName($file['name'])
                    ->usingFileName($file['name'])
                    ->toMediaCollection();
            });

        $oldFiles = collect($files)
            ->reject(function ($file) {
                return isset($file['path']);
            })
            ->toBase();

        // delete files
        $task->media
            ->toBase()
            ->filter(function (Media $file) use ($oldFiles) {
                return ! $oldFiles->contains('id', $file->id);
            })
            ->each(function (Media $file) use ($task) {
                $task->deleteMedia($file->id);
            });
    }

    protected function syncTags(Task $task, array $tags): void
    {
        foreach ($tags as $lang => $tag) {
            $task->syncTagsWithTypeAndLocale(mapFromSelect($tag), 'task_tags', $lang);
        }
    }

    public function createTaskStatus(Task $task): TaskStatus|null
    {
        $task->loadMissing('latestStatus', 'responsible', 'responsible.supervisor');

        $previousStatus = $task->latestStatus;

        if ($previousStatus && is_null($previousStatus->ended_at)) {
            $previousStatus->updateQuietly([
                'ended_at' => now(),
            ]);
        }

        // if it is done
        if ($task->status == Task::STATUS_DONE) {
            $task->updateQuietly([
                'done_at' => now(),
            ]);

            // notify supervisor
            if ($supervisor = $task->responsible->supervisor) {
                $supervisor->notify(new TaskDoneNotification($task));
            }

            return $previousStatus;
        }

        return $task->taskStatuses()->create([
            'status' => $task->status,
            'started_at' => now(),
            'ended_at' => null,
        ]);
    }

    protected function user()
    {
        return Auth::guard('admin')->user();
    }
}
