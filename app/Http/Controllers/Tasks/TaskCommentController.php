<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(CommentRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $newComment = new TaskComment();
        $newComment->body = $request->body;
        $newComment->admin_id = $this->user()->id;

        $task->comments()->save($newComment);

        return response()->json([
            'success' => __('Successfully created'),
        ]);
    }

    public function update(Request $request, TaskComment $comment)
    {
        $this->authorize('update', $comment->task);

        $comment->update(['body' => $request->updatedBody]);

        return response()->json([
            'success' => __('Successfully updated'),
        ]);
    }

    public function destroy(TaskComment $comment)
    {
        $this->authorize('update', $comment->task);

        $comment->delete();

        return response()->json([
            'success' => __('Successfully deleted'),
        ]);
    }
}
