<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveResource;
use App\Models\Admin;
use App\Models\Leave;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class UserProfileController extends Controller
{
    const TABS = [
        'overview' => 0,
        'notifications' => 1,
        'my-tasks' => 2,
        'leaves' => 3,
    ];

    /**
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        return Inertia::render('Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
            'date_time_formats' => mapForSelect(Admin::DATE_TIME_FORMATS, sameValue: true, translate: false),
            'tabs' => mapForSelect(self::TABS),
            'tab' => self::TABS[$request->get('tab')] ?? 0,
        ]);
    }

    public function leaves(): JsonResponse
    {
        $leaves = Leave::query()
            ->where('admin_id', $this->user()->id)
            ->with('admin')
            ->latest()
            ->get();

        $years = $leaves
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y');
            })
            ->unique()
            ->toArray();

        return response()->json([
            'types' => mapForSelect(Leave::TYPES),
            'years' => mapForSelect($years, true),
            'leaves' => LeaveResource::collection($leaves),
        ]);
    }

    /**
     * Get the current sessions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function sessions(Request $request)
    {

        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user('admin')->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )
            ->map(function ($session) use ($request) {
                $agent = $this->createAgent($session);

                return (object) [
                    'agent' => [
                        'is_desktop' => $agent->isDesktop(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $request->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
