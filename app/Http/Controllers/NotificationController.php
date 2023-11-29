<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        return Inertia::render('Notifications/Index', [
            'user' => AdminResource::make($this->user()),
        ]);
    }

    public function fetch()
    {
        return response()->json([
            'notifications' => $this->user()->notifications()->unread()->get(),
        ]);
    }

    public function fetchAll(Request $request)
    {
        $perPage = $request->input('per_page');
        $page = $request->input('page');
        $search = $request->input('search');

        $type = $request->input('notifiable_type');
        $id = $request->input('notifiable_id');

        $noti = DatabaseNotification::query()
            ->with('notifiable')
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $id);

        if ($search) {
            $noti
                ->where('data->title', 'LIKE', '%'.$search.'%')
                ->orWhere('created_at', 'LIKE', '%'.$search.'%');
        }

        $noti = $noti
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => NotificationResource::collection($noti),
            'total' => ceil($noti->total() / $request->input('per_page')),
        ]);
    }

    public function show(Request $request, $id)
    {
        $notification = DatabaseNotification::query()
            ->with('notifiable')
            ->findOrFail($id);

        $notification->updateQuietly([
            'read_at' => now(),
        ]);

        return Inertia::render('Notifications/Show', [
            'notification' => NotificationResource::make($notification),
        ]);
    }

    public function read(Request $request, $id)
    {
        DatabaseNotification::query()
            ->with('notifiable')
            ->findOrFail($id)
            ->updateQuietly([
                'read_at' => now(),
            ]);

        if ($request->input('redirect')) {
            return redirect()->route('notifications.index')->with('success', trans('Marked as read'));
        }

        return redirect()->back()->with('success', trans('Marked as read'));
    }

    public function unread(Request $request, $id)
    {
        DatabaseNotification::query()
            ->with('notifiable')
            ->findOrFail($id)
            ->updateQuietly([
                'read_at' => null,
            ]);

        if ($request->input('redirect')) {
            return redirect()->route('notifications.index')->with('success', trans('Marked as unread'));
        }

        return redirect()->back()->with('success', trans('Marked as unread'));
    }

    public function readAll()
    {
        $this->user()->notifications()->unread()->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => trans('Successfully updated'),
            'notifications' => $this->user()->fresh()->notifications()->unread()->get()->take(5),
        ]);
    }

    public function delete($id)
    {
        DatabaseNotification::query()
            ->with('notifiable')
            ->findOrFail($id)
            ->delete();

        return redirect()->back()->with('success', trans('Successfully deleted'));
    }
}
