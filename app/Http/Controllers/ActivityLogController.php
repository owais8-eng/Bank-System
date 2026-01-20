<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Get all activity logs with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by model type
        if ($request->has('model_type')) {
            $query->where('subject_type', $request->model_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Filter by event type
        if ($request->has('event')) {
            $query->where('event', $request->event);
        }

        // Search in description
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'activities' => $logs,
            'total' => $logs->total(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'per_page' => $logs->perPage(),
        ]);
    }

    /**
     * Get activity log for a specific model.
     */
    public function show(Request $request, $model, $id): JsonResponse
    {
        $modelClass = $this->resolveModelClass($model);

        if (!$modelClass) {
            return response()->json([
                'error' => 'Invalid model type.',
            ], 400);
        }

        $activities = Activity::where('subject_type', $modelClass)
            ->where('subject_id', $id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'model' => $model,
            'model_id' => $id,
            'activities' => $activities,
            'total_activities' => $activities->count(),
        ]);
    }

    /**
     * Get activity statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $fromDate = $request->get('from_date', now()->subDays(30)->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $stats = [
            'total_activities' => Activity::whereBetween('created_at', [$fromDate, $toDate])->count(),
            'by_event' => Activity::selectRaw('event, COUNT(*) as count')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->groupBy('event')
                ->pluck('count', 'event'),
            'by_model' => Activity::selectRaw('subject_type, COUNT(*) as count')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->groupBy('subject_type')
                ->pluck('count', 'subject_type'),
            'by_user' => Activity::selectRaw('causer_id, COUNT(*) as count')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
                ->with('causer:id,name,email')
                ->get()
                ->map(function ($item) {
                    return [
                        'user' => $item->causer,
                        'count' => $item->count,
                    ];
                }),
            'daily_activity' => Activity::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'statistics' => $stats,
        ]);
    }

    /**
     * Get recent activities for dashboard.
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $activities = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'recent_activities' => $activities,
            'count' => $activities->count(),
        ]);
    }

    /**
     * Resolve model class from string.
     */
    private function resolveModelClass(string $model): ?string
    {
        return match ($model) {
            'user', 'users' => 'App\Models\User',
            'account', 'accounts' => 'App\Models\Account',
            'transaction', 'transactions' => 'App\Models\Transaction',
            default => null,
        };
    }
}